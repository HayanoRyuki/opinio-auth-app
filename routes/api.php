<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sso\TokenController;

Route::post('/oauth/token', [TokenController::class, 'issue']);
Route::post('/dev/token', [\App\Http\Controllers\DevTokenController::class, 'issue']);
