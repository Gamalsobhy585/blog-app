<?php
// app/Events/AllNotificationsRead.php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AllNotificationsRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $user) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('user.' . $this->user->id . '.notifications');
    }

    public function broadcastAs(): string
    {
        return 'notifications.read-all';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => 'All notifications marked as read',
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}