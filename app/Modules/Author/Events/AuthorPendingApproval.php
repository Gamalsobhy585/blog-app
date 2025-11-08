<?php
namespace App\Modules\Author\Events;

use App\Models\Author;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuthorPendingApproval implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Author $author,
        public User $createdBy
    ) {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('admin-notifications');
    }

    public function broadcastAs(): string
    {
        return 'author.pending.approval';
    }

    public function broadcastWith(): array
    {
        return [
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
        ];
    }
}

