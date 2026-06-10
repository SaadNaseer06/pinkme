<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

if (! function_exists('storage_url')) {
    /**
     * Absolute URL for a path on the public disk (storage/app/public).
     * On cPanel (project root docroot), set USE_PUBLIC_URL_PREFIX=true for /public/storage/... URLs.
     */
    function storage_url(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        $path = (string) $path;
        if (preg_match('#^https?://#i', $path)) {
            if (config('app.asset_public_prefix')) {
                return preg_replace('#/storage/app/public/#', '/public/storage/', $path) ?? $path;
            }

            return $path;
        }

        return Storage::disk('public')->url(ltrim($path, '/'));
    }
}
