<?php

use App\Modules\Authorization\Controllers\PermissionsController;
use App\Modules\Authorization\Controllers\RolesController;
use App\Modules\Authorization\Controllers\UserRolesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:admin'])
    ->group(function () {
        Route::get('/roles', [RolesController::class, 'index']);
        Route::post('/roles', [RolesController::class, 'store']);

        Route::get('/permissions', [PermissionsController::class, 'index']);
        Route::post('/permissions', [PermissionsController::class, 'store']);
        Route::post('/users/{user}/role', [UserRolesController::class, 'store']);

    });