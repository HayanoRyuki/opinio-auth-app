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

// ログイン
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// ログアウト（GET = 確認画面 / POST = 実処理）
Route::get('/logout', function () {
    return view('auth.logout');
})->name('logout.confirm');

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// SSO start (Authorization Code flow)
Route::middleware('auth')->get('/sso/start', [SsoController::class, 'start']);

// 本番 TOP ページ
Route::get('/', function () {
    return view('welcome');
})->name('top');
