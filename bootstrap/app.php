<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(EnsureFrontendRequestsAreStateful::class); // ✅ Add Sanctum middleware
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
                $exceptions->render(function (Throwable $e) {
            if ( $e instanceof AccessDeniedHttpException ) {
                /* This may be overly specific, but I want to handle other
                   potential AccessDeniedHttpExceptions when I come
                   across them */
                if ( $e->getPrevious() instanceof AuthorizationException ) {
                    return redirect()
                        ->route('dashboard')
                        ->withErrors($e->getMessage());
                }
            }
        });
    })->create();

// return Application::configure(basePath: dirname(__DIR__))
//     ->withRouting(
//         web: __DIR__ . '/../routes/web.php',
//         api: __DIR__ . '/../routes/api.php',
//         commands: __DIR__ . '/../routes/console.php',
//         health: '/up',
//     )
//     ->withMiddleware(function (Middleware $middleware) {
//         $middleware->append(EnsureFrontendRequestsAreStateful::class); // ✅ Add Sanctum middleware
//     })->create();

