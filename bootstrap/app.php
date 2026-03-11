<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'admin.active' => \App\Http\Middleware\AdminActive::class,
        ]);

        $middleware->redirectGuestsTo(fn (\Illuminate\Http\Request $request) =>
            str_starts_with($request->path(), 'admin')
                ? route('admin.login')
                : route('admin.login')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
