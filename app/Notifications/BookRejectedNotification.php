<?php
// app/Notifications/BookPendingApprovalNotification.php

namespace App\Notifications;

use App\Models\Book;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BookRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Book $book,
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
            'type' => 'book_rejected',
            'book' => [
            //   book data here 
            ],
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
            'message' => "New book '{$this->book->name}' is rejected",
        ];
    }

    // ✅ This broadcasts to Pusher (real-time)
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id, // Notification ID for frontend tracking
            'type' => 'book_rejected',
            'book' => [
            //   book data here 

            ],
            'created_by' => [
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
            'message' => "New book '{$this->book->name}' is rejected",
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications')
        ];
    }

    public function broadcastAs(): string
    {
        return 'book.rejected';
    }
}