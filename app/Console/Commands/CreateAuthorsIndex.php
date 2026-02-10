<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;

class CreateAuthorsIndex extends Command
{
    protected $signature = 'es:create-authors-index';
    protected $description = 'Create the authors index in Elasticsearch';

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    public function handle(): int
    {
        $index = config('elasticsearch.index.authors', 'authors');

        // Check if exists
        if ($this->client->indices()->exists(['index' => $index])->asBool()) {
            $this->info("Index [$index] already exists.");
            return Command::SUCCESS;
        }

        // Create index
        $this->client->indices()->create([
            'index' => $index,
            'body' => [
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'uuid' => ['type' => 'keyword'],
                        'name' => ['type' => 'text'],
                        'bio' => ['type' => 'text'],
                        'nationality' => ['type' => 'keyword'],
                        'is_approved' => ['type' => 'boolean'],
                        'created_at' => ['type' => 'date'],
                    ],
                ],
            ],
        ]);

        $this->info("Index [$index] created successfully.");

        return Command::SUCCESS;
    }
}
