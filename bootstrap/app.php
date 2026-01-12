<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/web.php',
            __DIR__ . '/../routes/backend.php',
            __DIR__ . '/../routes/frontend.php',
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'binance/webhook',
            'binance/*',
        ]);

        // Handle database authentication errors globally
        $middleware->web(append: [
            \App\Http\Middleware\HandleDatabaseAuthErrors::class,
        ]);

        // Add query balancer middleware for performance monitoring
        $middleware->web(append: [
            \App\Http\Middleware\QueryBalancer::class,
        ]);

        // Register Spatie permission middleware aliases
        // Use custom permission middleware that bypasses super admin
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\SuperAdminPermission::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
