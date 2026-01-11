<?php

namespace App\Modules\Author\Actions;

use App\Models\Author;
use App\Modules\Author\DTOs\SearchAuthorData;
use Elastic\Elasticsearch\Client;
use Illuminate\Pagination\LengthAwarePaginator;

class ListAuthorsAction
{
    public function __construct(
        private readonly Client $client
    ) {}

    public function execute(SearchAuthorData $data): LengthAwarePaginator
    {
        $query = Author::query();

        // Approved scope
        if ($data->approved !== null) {
            if ($data->approved) {
                $query->approved();
            } else {
                $query->where('is_approved', false);
            }
        }

        // Elasticsearch full-text search
        if ($data->search) {
            $authorIds = $this->searchWithElasticsearch($data->search);

            // If ES gives no results -> force empty
            if (empty($authorIds)) {
                return new LengthAwarePaginator([], 0, $data->perPage, $data->page);
            }

            $query->whereIn('id', $authorIds);
        }

        if ($data->nationality) {
            $query->where('nationality', $data->nationality);
        }

        $query->orderBy($data->sortBy, $data->sortOrder);

        return $query->paginate($data->perPage, ['*'], 'page', $data->page);
    }

    private function searchWithElasticsearch(string $search): array
    {
        try {
            $index = config('elasticsearch.index.authors', 'authors');

          $response = $this->client->search([
                'index' => $index,
                'body'  => [
                    'query' => [
                        'bool' => [
                            'should' => [
                                [
                                    'multi_match' => [
                                        'query'     => $search,
                                        'fields'    => ['name^3', 'bio', 'nationality'],
                                        'fuzziness' => 'AUTO',
                                    ],
                                ],
                                [
                                    'wildcard' => [
                                        'name' => [
                                            'value' => "*{$search}*",
                                            'case_insensitive' => true
                                        ]
                                    ]
                                ],
                            ],
                        ],
                    ],
                    'size' => 1000,
                ],
            ]);
            // New client returns object -> use asArray()
            $hits = $response->asArray()['hits']['hits'] ?? [];

            return collect($hits)
                ->pluck('_source.id')
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            // Fallback to DB search
            return Author::where('name', 'like', "%{$search}%")
                ->orWhere('bio', 'like', "%{$search}%")
                ->orWhere('nationality', 'like', "%{$search}%")
                ->pluck('id')
                ->toArray();
        }
    }
}
