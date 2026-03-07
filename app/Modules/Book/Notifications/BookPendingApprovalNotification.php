<?php

namespace App\Modules\Book\Notifications;

use App\Models\Book;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BookPendingApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Book $book,
        public User $createdBy
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Book Pending Approval')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new book has been created and is pending your approval.')
            ->line('**Book Name:** ' . $this->book->name)
            ->line('**Nationality:** ' . ($this->book->nationality ?? 'N/A'))
            ->line('**Created By:** ' . $this->createdBy->name . ' (' . $this->createdBy->email . ')')
            ->action('Review Book', url('/admin/books/' . $this->book->uuid))
            ->line('Please review and approve or reject this book.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'book_pending_approval',
            'book' => [
                'uuid' => $this->book->uuid,
//  book model fileds

            ],
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
            'message' => "New book '{$this->book->name}' is pending approval",
            'created_at' => now()->toDateTimeString(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'book_pending_approval',
            'book' => [
                'uuid' => $this->book->uuid,
                //  book model fileds

            ],
            'created_by' => [
                'name' => $this->createdBy->name,
            ],
            'message' => "New book '{$this->book->name}' is pending approval",
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}