# cPanel Deployment Guide (public_html/pinkme)

## 1. Root .htaccess (public_html/pinkme/.htaccess)

Place this file in the **root** of your Laravel project (same level as `app/`, `public/`, etc.):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Redirect all requests to the public folder
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

## 2. Public .htaccess (public_html/pinkme/public/.htaccess)

Copy `public/.htaccess.cpanel` to `public/.htaccess` when deploying, or replace with this (adds `RewriteBase` for subdirectory):

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On
    RewriteBase /pinkme/public/

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## 3. .env Configuration

Set in your `.env`:

```
APP_URL=https://yourdomain.com/pinkme
```

## 4. Asset URL (if assets don't load)

In `config/app.php` or `AppServiceProvider`, you may need:

```php
// In AppServiceProvider::boot()
if (config('app.env') === 'production') {
    \URL::forceRootUrl(config('app.url'));
}
```

Or run:
```bash
php artisan config:cache
```

## 5. Folder Structure on cPanel

```
public_html/
└── pinkme/
    ├── .htaccess          ← Root .htaccess (from step 1)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/
    │   ├── .htaccess      ← Public .htaccess (from step 2)
    │   ├── index.php
    │   └── ...
    ├── resources/
    ├── routes/
    └── ...
```

## 6. Access URL

Your app will be available at: **https://yourdomain.com/pinkme**
