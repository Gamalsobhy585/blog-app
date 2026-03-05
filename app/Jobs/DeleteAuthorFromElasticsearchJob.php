<?php

namespace App\Jobs;

use Elastic\Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteAuthorFromElasticsearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $authorId) {}

    public function handle(Client $client): void
    {
        $index = config('elasticsearch.index.authors', 'authors');

        try {
            $client->delete([
                'index' => $index,
                'id'    => (string) $this->authorId,
            ]);
        } catch (\Throwable $e) {
            // ignore if not found
        }
    }
}