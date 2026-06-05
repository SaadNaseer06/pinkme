<?php

use App\Http\Middleware\LogSlowRequests;
use App\Http\Middleware\RedirectIfAuthenticatedByRole;
use App\Http\Middleware\RoleRestrictMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->append(LogSlowRequests::class);

        $middleware->alias([
            'redirect.by.role' => RedirectIfAuthenticatedByRole::class,
            'role.restrict' => RoleRestrictMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
