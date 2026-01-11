<?php
// app/Http/Controllers/NotificationController.php

namespace App\Modules\Notification\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;
use App\Events\NotificationRead; 
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = (int) $request->get('per_page', 10);
        
        $notifications = $user->notifications()
            ->latest()
            ->paginate($perPage);
            
        $unreadCount = $user->unreadNotifications()->count();
        
        return NotificationResource::collection($notifications)
            ->additional([
                'unread_count' => $unreadCount,
            ]);
    }

    // Mark single notification as read
    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        
        $notification = $user->notifications()->find($id);
        
        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found'
            ], 404);
        }
        
        // Mark as read in database
        $notification->markAsRead();
        
        // 🔥 Broadcast that this notification was read (real-time)
        broadcast(new NotificationRead($notification, $user))->toOthers();
        
        return response()->json([
            'message' => 'Notification marked as read',
            'data' => new NotificationResource($notification->fresh()),
            'unread_count' => $user->unreadNotifications()->count()
        ]);
    }

    // Mark all notifications as read
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        
        // Mark all as read
        $user->unreadNotifications->markAsRead();
        
        // 🔥 Broadcast that all were read
        broadcast(new \App\Events\AllNotificationsRead($user))->toOthers();
        
        return response()->json([
            'message' => 'All notifications marked as read',
            'unread_count' => 0
        ]);
    }
}