<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sso\TokenController;

Route::post('/oauth/token', [TokenController::class, 'issue']);
