<?php

namespace App\Jobs;

use App\Models\Author;
use Elastic\Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncAuthorToElasticsearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $authorId) {}

    public function handle(Client $client): void
    {
        $author = Author::query()->find($this->authorId);

        // If missing (deleted), just ignore here (Delete job handles deletion)
        if (! $author) {
            return;
        }

        $index = config('elasticsearch.index.authors', 'authors');

        $client->index([
            'index' => $index,
            'id'    => (string) $author->id,
            'body'  => [
                'id'          => $author->id,
                'uuid'        => $author->uuid,
                'name'        => $author->name,
                'slug'        => $author->slug,
                'bio'         => $author->bio,
                'nationality' => $author->nationality,
                'is_approved' => (bool) $author->is_approved,
                'created_at'  => optional($author->created_at)->toDateTimeString(),
            ],
        ]);
    }
}