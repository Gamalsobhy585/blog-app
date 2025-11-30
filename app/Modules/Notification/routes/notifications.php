<?php

use App\Modules\Notification\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // List notifications
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');
    // Get unread notifications count
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])
        ->name('notifications.unreadCount');

    // Mark multiple notifications as read
    Route::patch('/notifications/mark-as-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAsRead');
});