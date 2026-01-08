<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Internal\UserController;
use App\Http\Controllers\Sso\TokenController;

Route::post('/oauth/token', [TokenController::class, 'issue']);
Route::post('/dev/token', [\App\Http\Controllers\DevTokenController::class, 'issue']);

Route::middleware('internal.token')
    ->prefix('internal')
    ->group(function () {
        Route::post('/users', [UserController::class, 'store']);
    });
