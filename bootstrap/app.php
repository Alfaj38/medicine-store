<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\TenantMiddleware::class,
            \App\Http\Middleware\ReadOnlyScopeMiddleware::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'management' => \App\Http\Middleware\ManagementMiddleware::class,
            'company' => \App\Http\Middleware\CompanyMiddleware::class,
            'scope' => \App\Http\Middleware\ScopeMiddleware::class,
            'page_permission' => \App\Http\Middleware\CheckPagePermission::class,
            'reseller' => \App\Http\Middleware\ResellerMiddleware::class,
        ]);

        $middleware->redirectUsersTo(fn (Request $request) => $request->is('reseller/*') || $request->is('reseller') ? route('reseller.dashboard') : route('dashboard'));
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('reseller/*') || $request->is('reseller') ? route('reseller.login') : route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    ->withCommands([
        \App\Console\Commands\FetchBangladeshPharma::class,
    ])
    ->create();
