<?php
// app/Events/NotificationRead.php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;

class NotificationRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public DatabaseNotification $notification,
        public User $user
    ) {}

    // 🔥 Broadcast to user's private channel
    public function broadcastOn(): Channel
    {
        // Each user has their own notification channel
        return new PrivateChannel('user.' . $this->user->id . '.notifications');
    }

    public function broadcastAs(): string
    {
        return 'notification.read';
    }

    public function broadcastWith(): array
    {
        return [
            'notification_id' => $this->notification->id,
            'read_at' => $this->notification->read_at->toDateTimeString(),
        ];
    }
}