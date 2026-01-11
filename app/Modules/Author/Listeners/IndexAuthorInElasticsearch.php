<?php

namespace App\Modules\Author\Listeners;

use App\Modules\Author\Events\AuthorCreated;
use Elastic\Elasticsearch\Client;
use Illuminate\Contracts\Queue\ShouldQueue;

class IndexAuthorInElasticsearch implements ShouldQueue
{
    public function __construct(
        private readonly Client $client
    ) {}

    public function handle(AuthorCreated $event): void
    {
        try {
            $index = config('elasticsearch.index.authors', 'authors');

           
                $this->client->index([
                    'index' => $index,
                    'id'    => $event->author->id,
                    'body'  => [
                        'id'          => $event->author->id,
                        'uuid'        => $event->author->uuid,
                        'name'        => $event->author->name,
                        'bio'         => $event->author->bio,
                        'nationality' => $event->author->nationality,
                        'is_approved' => $event->author->is_approved,
                        'created_at'  => $event->author->created_at?->toIso8601String(),
                    ],
                ]);
            
        } catch (\Exception $e) {
            logger()->error('Failed to index author in Elasticsearch', [
                'author_id' => $event->author->id ?? null,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
