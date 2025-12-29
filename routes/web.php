<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Sso\SsoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'show']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout']);

// SSO start (Authorization Code flow)
Route::get('/sso/start', [SsoController::class, 'start']);

Route::get('/', function() {
    return 'Auth-app Laravel is running!';
});
