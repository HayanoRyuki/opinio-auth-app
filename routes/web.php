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

// ログイン / ログアウト
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout']);

// SSO start (Authorization Code flow)
Route::get('/sso/start', [SsoController::class, 'start']);

// 本番 TOP ページ
Route::get('/', function () {
    return view('welcome'); // ここを本番TOPビューに差し替え可能
})->name('top');
