<?php

namespace App\Modules\Author\Actions;

use App\Models\Author;
use App\Modules\Author\DTOs\SearchAuthorData;
use Elastic\Elasticsearch\Client;
use Illuminate\Pagination\CursorPaginator;

class ListAuthorsAction
{
    public function __construct(
        private readonly Client $client
    ) {}

    public function execute(SearchAuthorData $data): CursorPaginator
    {
        $query = Author::query();

        if ($data->approved !== null) {
            $query->where('is_approved', $data->approved);
        }

        if ($data->nationality) {
            $query->where('nationality', $data->nationality);
        }

        if ($data->search) {
            $authorIds = $this->searchWithElasticsearch($data->search);

            if (empty($authorIds)) {
                return new CursorPaginator([], 'cursor', $data->perPage, [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]);
            }

            $query->whereIn('id', $authorIds);
        }

        $sortBy = in_array($data->sortBy, ['created_at', 'name', 'id'], true) ? $data->sortBy : 'created_at';
        $sortOrder = strtolower($data->sortOrder) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortOrder)
              ->orderBy('id', $sortOrder); 

        return $query->cursorPaginate(
            perPage: $data->perPage,
            columns: ['*'],
            cursorName: 'cursor',
            cursor: $data->cursor ? \Illuminate\Pagination\Cursor::fromEncoded($data->cursor) : null
        );
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
                                        'fields'    => ['name^3', 'bio', 'nationality', 'slug^2'],
                                        'fuzziness' => 'AUTO',
                                    ],
                                ],
                                [
                                    'wildcard' => [
                                        'name' => [
                                            'value' => "*{$search}*",
                                            'case_insensitive' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'size' => 1000,
                ],
            ]);

            $hits = $response->asArray()['hits']['hits'] ?? [];

            return collect($hits)
                ->pluck('_source.id')
                ->filter()
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            return Author::query()
                ->where('name', 'like', "%{$search}%")
                ->orWhere('bio', 'like', "%{$search}%")
                ->orWhere('nationality', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->pluck('id')
                ->toArray();
        }
    }
}