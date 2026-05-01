<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

if (! function_exists('storage_url')) {
    /**
     * Absolute URL for a path on the public disk (storage/app/public).
     * Honors USE_PUBLIC_URL_PREFIX when the web root is the project directory (not public/).
     */
    function storage_url(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        $path = (string) $path;
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        return Storage::disk('public')->url(ltrim($path, '/'));
    }
}
