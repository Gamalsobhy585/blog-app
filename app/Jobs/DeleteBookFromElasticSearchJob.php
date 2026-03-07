<?php

namespace App\Jobs;

use Elastic\Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteBookFromElasticsearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $bookId) {}

    public function handle(Client $client): void
    {
        $index = config('elasticsearch.index.books', 'books');

        try {
            $client->delete([
                'index' => $index,
                'id'    => (string) $this->bookId,
            ]);
        } catch (\Throwable $e) {
            // ignore if not found
        }
    }
}