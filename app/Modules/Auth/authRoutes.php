<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;

Route::prefix('auth')->group(function () {

    // Public routes (throttled)
    Route::middleware('throttle:auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

        // Password endpoints usually need stricter throttling
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
            ->name('auth.forgot-password')
            ->middleware('throttle:password');

        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->name('auth.reset-password')
            ->middleware('throttle:password');
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});