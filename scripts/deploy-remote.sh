#!/usr/bin/env bash
# Runs on the cPanel server after git pull (invoked by GitHub Actions).
set -euo pipefail

cd "$(dirname "$0")/.."

echo "=== PinkMe remote deploy ==="

if [ ! -f .env ]; then
  echo "Creating .env from .env.example..."
  cp .env.example .env
  php artisan key:generate --force
fi

if ! grep -q 'APP_KEY=base64:' .env 2>/dev/null; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
fi

if [ -n "${APP_URL:-}" ]; then
  echo "Setting APP_URL=${APP_URL}"
  sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env
  if grep -q '^ASSET_URL=' .env; then
    sed -i "s|^ASSET_URL=.*|ASSET_URL=${APP_URL}|" .env
  fi
fi

echo "=== Installing Composer dependencies ==="
if command -v composer >/dev/null 2>&1; then
  composer install --no-dev --optimize-autoloader --no-interaction
else
  curl -sS https://getcomposer.org/installer | php -d allow_url_fopen=On -- --install-dir=. --filename=composer.phar
  php -d allow_url_fopen=On composer.phar install --no-dev --optimize-autoloader --no-interaction
fi

echo "=== Setting permissions ==="
chmod -R 775 storage bootstrap/cache

php artisan storage:link 2>/dev/null || true

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Rebuilding caches ==="
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan queue:restart 2>/dev/null || true

echo "=== Remote deploy complete ==="
