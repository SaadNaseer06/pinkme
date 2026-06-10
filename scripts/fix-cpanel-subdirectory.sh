#!/usr/bin/env bash
# Fix Apache 404/500 on cPanel subdirectory installs (e.g. /pinkme/).
# Run on the server: bash scripts/fix-cpanel-subdirectory.sh
set -euo pipefail

cd "$(dirname "$0")/.."

APP_URL="$(grep -E '^APP_URL=' .env 2>/dev/null | head -1 | cut -d= -f2- | tr -d '"' | tr -d "'" | tr -d '\r' || true)"
URL_PATH="$(echo "${APP_URL%/}" | sed -E 's#^https?://[^/]+##' )"
FOLDER="$(basename "$URL_PATH")"

if [ -z "$URL_PATH" ] || [ "$URL_PATH" = "" ]; then
  echo "ERROR: APP_URL in .env must include the subdirectory path, e.g. https://serverlinktestwebsites.com/pinkme"
  exit 1
fi

echo "Using URL path: ${URL_PATH} (folder: ${FOLDER})"

cat > .htaccess <<EOF
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^${URL_PATH}/public/ [NC]
    RewriteRule ^(.*)$ public/\$1 [L]
</IfModule>
EOF

if [ -f public/.htaccess.cpanel ]; then
  sed "s|RewriteBase /pinkme/public/|RewriteBase ${URL_PATH}/public/|" public/.htaccess.cpanel > public/.htaccess
else
  echo "ERROR: public/.htaccess.cpanel not found"
  exit 1
fi

PARENT_HTACCESS="$(dirname "$(pwd)")/.htaccess"
BEGIN="# BEGIN PINKME-LARAVEL-${FOLDER}"
END="# END PINKME-LARAVEL-${FOLDER}"

echo "Updating ${PARENT_HTACCESS}"
touch "$PARENT_HTACCESS"
if grep -qF "$BEGIN" "$PARENT_HTACCESS" 2>/dev/null; then
  awk -v begin="$BEGIN" -v end="$END" '
    $0 == begin { skip=1; next }
    $0 == end { skip=0; next }
    !skip { print }
  ' "$PARENT_HTACCESS" > "${PARENT_HTACCESS}.tmp"
  mv "${PARENT_HTACCESS}.tmp" "$PARENT_HTACCESS"
fi

cat >> "$PARENT_HTACCESS" <<EOF

${BEGIN}
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^${FOLDER}/?\$ ${FOLDER}/public/ [L]
RewriteCond %{REQUEST_URI} !^${URL_PATH}/public/ [NC]
RewriteRule ^${FOLDER}(?:/(.*))?\$ ${FOLDER}/public/\$1 [L]
</IfModule>
${END}
EOF

mkdir -p storage/app/public storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

if [ -e public/storage ] && [ ! -L public/storage ]; then
  echo "Removing non-symlink public/storage"
  rm -rf public/storage
fi
if [ ! -L public/storage ]; then
  ln -sf ../storage/app/public public/storage
  echo "Created public/storage symlink"
fi

PHP="/usr/local/bin/ea-php82"
if [ ! -x "$PHP" ]; then
  PHP="php"
fi

if [ ! -f vendor/autoload.php ]; then
  echo "Installing Composer dependencies..."
  if [ ! -f composer.phar ]; then
    curl -sS -o composer.phar https://getcomposer.org/download/latest-stable/composer.phar
  fi
  "$PHP" composer.phar install --no-dev --optimize-autoloader --no-interaction --no-scripts
fi

grep -q '^USE_PUBLIC_URL_PREFIX=' .env && \
  sed -i 's|^USE_PUBLIC_URL_PREFIX=.*|USE_PUBLIC_URL_PREFIX=true|' .env || \
  echo 'USE_PUBLIC_URL_PREFIX=true' >> .env

grep -q '^STORAGE_PUBLIC_USE_FULL_PATH=' .env && \
  sed -i 's|^STORAGE_PUBLIC_USE_FULL_PATH=.*|STORAGE_PUBLIC_USE_FULL_PATH=false|' .env || \
  echo 'STORAGE_PUBLIC_USE_FULL_PATH=false' >> .env

"$PHP" artisan config:clear 2>/dev/null || true
"$PHP" artisan route:clear 2>/dev/null || true
"$PHP" artisan config:cache 2>/dev/null || true
"$PHP" artisan route:cache 2>/dev/null || true

echo ""
echo "Done. Test these URLs:"
echo "  ${APP_URL}/register?tab=login"
echo "  ${APP_URL}/admin/dashboard  (should redirect to staff login if not logged in)"
echo "If still broken: tail -30 storage/logs/laravel.log"
