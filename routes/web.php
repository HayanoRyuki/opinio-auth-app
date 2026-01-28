<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Sso\SsoController;
use Inertia\Inertia;

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
    return Inertia::render('Auth/Logout');
})->name('logout.confirm');

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// プロフィール編集（認証必須）
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// SSO start (Authorization Code flow)
Route::middleware('auth')->get('/sso/start', [SsoController::class, 'start']);

// 本番 TOP ページ
Route::get('/', [LoginController::class, 'welcome'])->name('top');
