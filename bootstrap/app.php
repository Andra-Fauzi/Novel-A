<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

// use App\Http\Middleware\CheckAdmin;
// use App\Http\Middleware\CheckAuthor;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
        $middleware->alias([
            'check_admin' => \App\Http\Middleware\CheckAdmin::class,
            'check_author' => \App\Http\Middleware\CheckAuthor::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
