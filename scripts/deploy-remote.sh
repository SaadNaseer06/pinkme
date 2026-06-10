#!/usr/bin/env bash
# Runs on the cPanel server after git pull (invoked by GitHub Actions).
set -euo pipefail

cd "$(dirname "$0")/.."

detect_php() {
  if [ -n "${PHP_BIN:-}" ] && command -v "${PHP_BIN}" >/dev/null 2>&1; then
    echo "${PHP_BIN}"
    return
  fi
  local candidate
  for candidate in \
    /usr/local/bin/ea-php83 \
    /usr/local/bin/ea-php82 \
    /opt/cpanel/ea-php83/root/usr/bin/php \
    /opt/cpanel/ea-php82/root/usr/bin/php \
    php; do
    if command -v "$candidate" >/dev/null 2>&1; then
      echo "$candidate"
      return
    fi
  done
  echo "php"
}

php_can_run_artisan() {
  "$PHP" -r 'exit(function_exists("proc_open") ? 0 : 1);' 2>/dev/null
}

PHP="$(detect_php)"
echo "Using PHP $("$PHP" -r 'echo PHP_VERSION;') ($PHP)"

if ! php_can_run_artisan; then
  echo "NOTE: proc_open is disabled for this PHP CLI."
  echo "      Composer will skip scripts; package manifest is uploaded from CI."
fi

run_composer() {
  if [ ! -f composer.phar ]; then
    echo "Downloading composer.phar..."
    curl -sS -o composer.phar https://getcomposer.org/download/latest-stable/composer.phar
  fi
  # Always use cPanel PHP binary — system `composer` may use a restricted PHP.
  "$PHP" composer.phar "$@"
}

artisan() {
  "$PHP" artisan "$@"
}

link_public_storage() {
  echo "=== Linking public/storage ==="
  if [ -L public/storage ]; then
    echo "public/storage symlink already exists"
    return 0
  fi
  if [ -e public/storage ] && [ ! -L public/storage ]; then
    echo "WARNING: public/storage exists and is not a symlink; skipping"
    return 0
  fi
  mkdir -p storage/app/public
  ln -sf ../storage/app/public public/storage
  echo "Created public/storage -> ../storage/app/public"
}

url_path_from_app_url() {
  local url="${1:-}"
  url="${url%/}"
  if [[ "$url" =~ ^https?://[^/]+(/.*)$ ]]; then
    echo "${BASH_REMATCH[1]}"
  else
    echo ""
  fi
}

configure_apache_rewrite() {
  local app_url="${APP_URL:-}"
  if [ -z "$app_url" ] && [ -f .env ]; then
    app_url="$(grep -E '^APP_URL=' .env | head -1 | cut -d= -f2- | tr -d '"' | tr -d "'" | tr -d '\r')"
  fi

  local url_path
  url_path="$(url_path_from_app_url "$app_url")"

  if [ -n "$url_path" ]; then
    echo "=== Configuring Apache for subdirectory: ${url_path} ==="
    cat > .htaccess <<EOF
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^${url_path}/public/ [NC]
    RewriteRule ^(.*)$ public/\$1 [L]
</IfModule>
EOF
    if [ -f public/.htaccess.cpanel ]; then
      sed "s|RewriteBase /pinkme/public/|RewriteBase ${url_path}/public/|" public/.htaccess.cpanel > public/.htaccess
    else
      echo "WARNING: public/.htaccess.cpanel missing"
    fi
  else
    echo "=== Configuring Apache for domain root ==="
    cat > .htaccess <<'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
EOF
    if git show HEAD:public/.htaccess >/dev/null 2>&1; then
      git show HEAD:public/.htaccess > public/.htaccess
    fi
  fi
}

echo "=== PinkMe remote deploy ==="

if [ ! -f .env ]; then
  echo "Creating .env from .env.example..."
  cp .env.example .env
  artisan key:generate --force
fi

if ! grep -q 'APP_KEY=base64:' .env 2>/dev/null; then
  echo "Generating APP_KEY..."
  artisan key:generate --force
fi

if [ -n "${APP_URL:-}" ]; then
  echo "Setting APP_URL=${APP_URL}"
  sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env
  if grep -q '^ASSET_URL=' .env; then
    sed -i "s|^ASSET_URL=.*|ASSET_URL=${APP_URL}|" .env
  fi
fi

if grep -q '^USE_PUBLIC_URL_PREFIX=' .env 2>/dev/null; then
  sed -i 's|^USE_PUBLIC_URL_PREFIX=.*|USE_PUBLIC_URL_PREFIX=true|' .env
else
  echo 'USE_PUBLIC_URL_PREFIX=true' >> .env
fi

configure_apache_rewrite

echo "=== Installing Composer dependencies ==="
# --no-scripts avoids `artisan package:discover` during install (needs proc_open on cPanel).
run_composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

if [ ! -f bootstrap/cache/packages.php ]; then
  echo "ERROR: bootstrap/cache/packages.php is missing."
  echo "       The deploy workflow should upload it from CI before migrations run."
  exit 1
fi

echo "=== Setting permissions ==="
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

link_public_storage

echo "=== Running migrations ==="
artisan migrate --force

echo "=== Rebuilding caches ==="
artisan config:clear
artisan cache:clear
artisan config:cache
artisan route:cache
artisan view:cache

artisan queue:restart 2>/dev/null || true

echo "=== Remote deploy complete ==="
