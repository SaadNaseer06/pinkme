# cPanel Deployment Guide – Fixing HTTP 500

## Your Setup
- **URL:** https://serverlinktestwebsites.com/pinkme/
- **Path:** `~/public_html/pinkme` (or `/home/serverlinkitestwe/public_html/pinkme`)
- **Domain:** serverlinktestwebsites.com

---

## Step 1: .htaccess (Already Fixed in Repo)

The repo now includes:

**Root `.htaccess`** (in `pinkme/`):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**`public/.htaccess`** – includes `RewriteBase /pinkme/public/` for subdirectory routing.

---

## Step 2: First-Time Setup on Server (SSH)

SSH into your cPanel account, then:

```bash
cd ~/public_html/pinkme
```

### 2a. Create .env
```bash
# If .env doesn't exist:
cp .env.example .env
php artisan key:generate --force
```

### 2b. Edit .env
```bash
nano .env   # or use cPanel File Manager
```

Set at least:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://serverlinktestwebsites.com/pinkme

# Database (use MySQL from cPanel)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 2c. Run the fix script
```bash
bash scripts/deploy-fix.sh
```

### 2d. Run migrations
```bash
php artisan migrate --force
```

---

## Step 3: Permissions

Ensure the web server can write to Laravel directories:

```bash
chmod -R 775 storage bootstrap/cache
```

On some cPanel setups you may need:
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework
```

---

## Step 4: Check the Error Log

If you still get 500:

```bash
tail -50 ~/public_html/pinkme/storage/logs/laravel.log
```

Or in cPanel: **Metrics → Errors** or **File Manager → storage/logs/laravel.log**.

---

## Step 5: Common 500 Causes

| Cause | Fix |
|-------|-----|
| Missing APP_KEY | `php artisan key:generate --force` |
| Wrong permissions | `chmod -R 775 storage bootstrap/cache` |
| .env missing | Copy from .env.example, set APP_KEY and DB_* |
| Wrong APP_URL | Set `APP_URL=https://serverlinktestwebsites.com/pinkme` |
| mod_rewrite off | Enable in cPanel or contact host |
| PHP version | Use PHP 8.1+ (cPanel MultiPHP) |
| Missing vendor/ | `composer install --no-dev` |

---

## GitHub Actions Deploy

The workflow at `.github/workflows/deploy.yml` will:

1. Pull latest from `main`
2. Create .env from .env.example if missing
3. Generate APP_KEY if empty
4. Run `composer install`
5. Set permissions
6. Run migrations
7. Clear and rebuild caches

**Required GitHub Secrets:**
- `CPANEL_HOST` = 66.29.149.231
- `CPANEL_USERNAME` = serverlinkitestwe
- `CPANEL_SSH_KEY` = Your private SSH key
- `CPANEL_SSH_PORT` = 22 (or your port)
- `DEPLOY_PATH` = /home/serverlinkitestwe/public_html/pinkme

---

## Quick Manual Fix (One-Time)

```bash
cd ~/public_html/pinkme
cp .env.example .env
php artisan key:generate --force
# Edit .env: APP_URL, DB_*, APP_DEBUG=false
composer install --no-dev --optimize-autoloader
chmod -R 775 storage bootstrap/cache
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```
