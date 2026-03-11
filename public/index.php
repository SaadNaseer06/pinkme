<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Strip subdirectory from REQUEST_URI when app is in a subdirectory (e.g. /pinkme)
$envPath = __DIR__.'/../.env';
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if (str_starts_with($line, 'APP_URL=')) {
            $url = trim(substr($line, 8), " \t\n\r\0\x0B\"'");
            $basePath = parse_url($url, PHP_URL_PATH);
            if ($basePath && $basePath !== '/' && isset($_SERVER['REQUEST_URI'])) {
                $uri = $_SERVER['REQUEST_URI'];
                $uriPath = parse_url($uri, PHP_URL_PATH) ?: '/';
                $query = parse_url($uri, PHP_URL_QUERY);
                if (str_starts_with($uriPath, $basePath)) {
                    $newPath = substr($uriPath, strlen($basePath)) ?: '/';
                    $_SERVER['REQUEST_URI'] = $newPath . ($query ? '?' . $query : '');
                }
            }
            break;
        }
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
