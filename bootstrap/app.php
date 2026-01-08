<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerifyJwt;
use App\Http\Middleware\InternalToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // JWT 検証（ATS 用）
        $middleware->alias([
            'verify.jwt'    => VerifyJwt::class,
            'internal.token'=> InternalToken::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
