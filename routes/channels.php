<?php
// routes/channels.php

use Illuminate\Support\Facades\Broadcast;

// Private channel for user notifications
Broadcast::channel('user.{userId}.notifications', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Public channel for all admins
Broadcast::channel('admin-notifications', function ($user) {
    return $user->role === 1; // Only admins can listen
});