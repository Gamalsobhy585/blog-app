<?php

namespace App\Modules\Book\Listeners;

use App\Modules\Book\Events\BookCreated;
use Elastic\Elasticsearch\Client;
use Illuminate\Contracts\Queue\ShouldQueue;

class IndexBookInElasticsearch implements ShouldQueue
{
    public function __construct(
        private readonly Client $client
    ) {}

    public function handle(BookCreated $event): void
    {
        try {
            $index = config('elasticsearch.index.books', 'books');

           
                $this->client->index([
                    'index' => $index,
                    'id'    => $event->book->id,
                    'body'  => [
                        'id'          => $event->book->id,
                        'uuid'        => $event->book->uuid,
                        //  book model fileds

                        'is_approved' => $event->book->is_approved,
                        'created_at'  => $event->book->created_at?->toIso8601String(),
                    ],
                ]);
            
        } catch (\Exception $e) {
            logger()->error('Failed to index book in Elasticsearch', [
                'book_id' => $event->book->id ?? null,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
