<?php

namespace App\Notifications;

use App\Models\Book;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BookPendingApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Book $book,
        public User $createdBy
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'book_pending_approval',
            'book' => [
                'id' => $this->book->id,
                'uuid' => $this->book->uuid,
                'title' => $this->book->title,
                'slug' => $this->book->slug,
                'status' => is_object($this->book->status) ? $this->book->status->value : $this->book->status,
                'is_approved' => (bool) $this->book->is_approved,
                'author_id' => $this->book->author_id,
                'cover_image' => $this->book->cover_image,
                'created_at' => optional($this->book->created_at)?->toDateTimeString(),
            ],
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
            'message' => "New book '{$this->book->title}' is pending approval",
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'book_pending_approval',
            'book' => [
                'id' => $this->book->id,
                'uuid' => $this->book->uuid,
                'title' => $this->book->title,
                'slug' => $this->book->slug,
                'status' => is_object($this->book->status) ? $this->book->status->value : $this->book->status,
                'is_approved' => (bool) $this->book->is_approved,
                'author_id' => $this->book->author_id,
                'cover_image' => $this->book->cover_image,
                'created_at' => optional($this->book->created_at)?->toDateTimeString(),
            ],
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
            'message' => "New book '{$this->book->title}' is pending approval",
            'timestamp' => now()->toDateTimeString(),
        ]);
    }


    public function broadcastOn(): Channel
    {
        return new Channel('admin-notifications');
    }


    public function broadcastAs(): string
    {
        return 'book.pending.approval';
    }
}