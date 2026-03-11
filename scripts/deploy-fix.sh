#!/bin/bash
# Run this script on the cPanel server via SSH to fix common deployment issues
# Usage: bash scripts/deploy-fix.sh (run from project root: ~/public_html/pinkme)

set -e
cd "$(dirname "$0")/.."

echo "=== Pinkme Deployment Fix Script ==="
echo ""

# 1. Check .env
echo "1. Checking .env..."
if [ ! -f .env ]; then
  echo "   .env not found. Creating from .env.example..."
  cp .env.example .env
  php artisan key:generate --force
  echo "   Done. IMPORTANT: Edit .env and set APP_URL, DB_*, etc."
else
  echo "   .env exists"
  if ! grep -q 'APP_KEY=base64:' .env 2>/dev/null; then
    echo "   APP_KEY missing. Generating..."
    php artisan key:generate --force
  else
    echo "   APP_KEY is set"
  fi
fi
echo ""

# 2. Permissions
echo "2. Setting permissions..."
chmod -R 775 storage bootstrap/cache
echo "   storage/ and bootstrap/cache/ set to 775"
echo ""

# 3. Composer
echo "3. Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
echo ""

# 4. Caches
echo "4. Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo ""

# 5. Rebuild caches
echo "5. Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo ""

# 6. Show last log entries (if any errors)
echo "6. Last 20 lines of laravel.log (if exists):"
if [ -f storage/logs/laravel.log ]; then
  tail -20 storage/logs/laravel.log
else
  echo "   No log file yet"
fi
echo ""

echo "=== Done. If you still see 500, check: ==="
echo "  - APP_URL in .env should be: https://serverlinktestwebsites.com/pinkme"
echo "  - DB_* settings if using MySQL"
echo "  - storage/logs/laravel.log for the actual error"
echo "  - .htaccess in root redirects to public/"
echo "  - public/.htaccess has RewriteBase /pinkme/public/"
