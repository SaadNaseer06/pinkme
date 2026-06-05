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

PHP="$(detect_php)"
echo "Using PHP $("$PHP" -r 'echo PHP_VERSION;') ($PHP)"

run_composer() {
  if command -v composer >/dev/null 2>&1; then
    composer "$@"
    return
  fi
  if [ ! -f composer.phar ]; then
    echo "Downloading composer.phar..."
    curl -sS -o composer.phar https://getcomposer.org/download/latest-stable/composer.phar
  fi
  "$PHP" composer.phar "$@"
}

artisan() {
  "$PHP" artisan "$@"
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

echo "=== Installing Composer dependencies ==="
run_composer install --no-dev --optimize-autoloader --no-interaction

echo "=== Setting permissions ==="
chmod -R 775 storage bootstrap/cache

artisan storage:link 2>/dev/null || true

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
