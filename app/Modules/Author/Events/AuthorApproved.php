<?php
namespace App\Modules\Author\Events;

use App\Models\Author;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuthorApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Author $author)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('author-updates');
    }

    public function broadcastAs(): string
    {
        return 'author.approved';
    }

    public function broadcastWith(): array
    {
        return [
            'author' => [
                'uuid' => $this->author->uuid,
                'name' => $this->author->name,
            ],
            'message' => "Author '{$this->author->name}' has been approved",
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
