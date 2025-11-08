<?php

namespace App\Modules\Author\Notifications;

use App\Models\Author;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AuthorPendingApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Author $author,
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
            ->subject('New Author Pending Approval')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new author has been created and is pending your approval.')
            ->line('**Author Name:** ' . $this->author->name)
            ->line('**Nationality:** ' . ($this->author->nationality ?? 'N/A'))
            ->line('**Created By:** ' . $this->createdBy->name . ' (' . $this->createdBy->email . ')')
            ->action('Review Author', url('/admin/authors/' . $this->author->uuid))
            ->line('Please review and approve or reject this author.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'author_pending_approval',
            'author' => [
                'uuid' => $this->author->uuid,
                'name' => $this->author->name,
                'nationality' => $this->author->nationality,
                'bio' => $this->author->bio,
            ],
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
            'message' => "New author '{$this->author->name}' is pending approval",
            'created_at' => now()->toDateTimeString(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'author_pending_approval',
            'author' => [
                'uuid' => $this->author->uuid,
                'name' => $this->author->name,
                'nationality' => $this->author->nationality,
            ],
            'created_by' => [
                'name' => $this->createdBy->name,
            ],
            'message' => "New author '{$this->author->name}' is pending approval",
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}