<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;
use Throwable;

class CreateBooksIndex extends Command
{
    protected $signature = 'es:create-books-index {--force : Delete and recreate if already exists}';
    protected $description = 'Create the books index in Elasticsearch';

    public function __construct(private readonly Client $client)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $index = config('elasticsearch.index.books', 'books');

        try {
            $exists = $this->client->indices()->exists(['index' => $index])->asBool();

            if ($exists) {
                if (! $this->option('force')) {
                    $this->info("Index [$index] already exists. Use --force to recreate it.");
                    return Command::SUCCESS;
                }

                $this->client->indices()->delete(['index' => $index]);
                $this->warn("Index [$index] deleted.");
            }
            $this->client->indices()->create([
                'index' => $index,
                'body'  => [
                    'settings' => [
                        'number_of_replicas' => 0, // single-node fix
                    ],
                    'mappings' => [
                        'properties' => [
                            'id'               => ['type' => 'integer'],
                            'title'            => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                            'description'      => ['type' => 'text'],
                            'slug'             => ['type' => 'keyword'],
                            'status'           => ['type' => 'integer'],
                            'is_approved'      => ['type' => 'boolean'],
                            'author_id'        => ['type' => 'integer'],
                            'available_copies' => ['type' => 'integer'],
                            'price'            => ['type' => 'float'],
                            'created_at'       => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||strict_date_optional_time'],
                        ],
                    ],
                ],
            ]);
            $this->info("Index [$index] created successfully.");
            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->error("Failed to create index [$index]: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}