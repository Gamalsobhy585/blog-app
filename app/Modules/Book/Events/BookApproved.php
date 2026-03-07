<?php
namespace App\Modules\Book\Events;

use App\Models\Book;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Book $book)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('book-updates');
    }

    public function broadcastAs(): string
    {
        return 'book.approved';
    }

    public function broadcastWith(): array
    {
        return [
            'book' => [
                'uuid' => $this->book->uuid,
                'name' => $this->book->name,
            ],
            'message' => "Book '{$this->book->name}' has been approved",
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
