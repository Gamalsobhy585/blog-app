<?php
namespace App\Modules\Book\Events;

use App\Models\Book;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookPendingApproval implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Book $author,
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
//  book model fileds

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

