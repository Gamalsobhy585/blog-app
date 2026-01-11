<?php
// app/Notifications/AuthorPendingApprovalNotification.php

namespace App\Notifications;

use App\Models\Author;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class AuthorPendingApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Author $author,
        public User $createdBy
    ) {}

    // ✅ Send to BOTH database AND broadcast (Pusher)
    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    // ✅ This saves to database (notifications table)
    public function toArray($notifiable): array
    {
        return [
            'type' => 'author_pending_approval',
            'author' => [
                'uuid' => $this->author->uuid,
                'name' => $this->author->name,
                'nationality' => $this->author->nationality,
            ],
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
            'message' => "New author '{$this->author->name}' is pending approval",
        ];
    }

    // ✅ This broadcasts to Pusher (real-time)
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id, // Notification ID for frontend tracking
            'type' => 'author_pending_approval',
            'author' => [
                'uuid' => $this->author->uuid,
                'name' => $this->author->name,
                'nationality' => $this->author->nationality,
            ],
            'created_by' => [
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
            'message' => "New author '{$this->author->name}' is pending approval",
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    // ✅ Customize broadcast channel per user
    public function broadcastOn(): array
    {
        // This will broadcast to: private-App.Models.User.{userId}
        return ['admin-notifications'];
    }

    public function broadcastAs(): string
    {
        return 'author.pending.approval';
    }
}