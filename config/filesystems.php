<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            // Browser URL for files on this disk (must match how the web server exposes storage).
            // - Normal (docroot = public/): {APP_URL}/storage/... via public/storage symlink.
            // - Project root in public_html + USE_PUBLIC_URL_PREFIX: use /storage/app/public/...
            //   NOT /public/storage/... (that path often 404s on cPanel).
            // - Override: STORAGE_PUBLIC_USE_FULL_PATH=true forces /storage/app/public/...
            // - Rare legacy: STORAGE_PUBLIC_LEGACY_SYMLINK_URL=true restores /public/storage/... when prefixed.
            'url' => (static function (): string {
                $base = rtrim(env('APP_URL'), '/');
                if (filter_var(env('STORAGE_PUBLIC_LEGACY_SYMLINK_URL', false), FILTER_VALIDATE_BOOLEAN)) {
                    return $base.(config('app.asset_public_prefix') ? '/public' : '').'/storage';
                }
                if (filter_var(env('STORAGE_PUBLIC_USE_FULL_PATH', false), FILTER_VALIDATE_BOOLEAN)
                    || config('app.asset_public_prefix')) {
                    return $base.'/storage/app/public';
                }

                return $base.'/storage';
            })(),
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
