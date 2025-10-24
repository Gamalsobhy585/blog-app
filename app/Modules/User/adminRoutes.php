<?php
use App\Modules\User\Controllers\AdminUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'can:isAdmin'])->group(function () {
    Route::post('/users/toggle', [AdminUserController::class, 'toggleStatus']);
    Route::get('/users', [AdminUserController::class, 'index']);

});
