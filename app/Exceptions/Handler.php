<?php

namespace App\Exceptions;

// use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Log;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // protected function unauthenticated($request, AuthenticationException $exception)
    // {
    //     return response()->json(['message' => 'Unauthenticated.'], 401);
    //     // if ($request->expectsJson()) {
    //     //     return response()->json(['message' => 'Unauthenticated.'], 401);
    //     // }
    // }

    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        
        return response()->json(['message' => 'Unauthenticated'], 401);
    }
}
