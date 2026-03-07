<?php

namespace App\Modules\Book\Actions;

use App\Models\Book;
use Elastic\Elasticsearch\Client;
use Illuminate\Pagination\Cursor;
use Illuminate\Pagination\CursorPaginator;
use App\Modules\Book\DTOs\SearchBookData;
use App\Modules\Book\Exceptions\BookSearchException;
use App\Modules\Book\Exceptions\BookListingException;
use Throwable;

class ListBooksAction
{
    public function __construct(
        private readonly Client $client
    ) {}

    public function execute(SearchBookData $data): CursorPaginator
    {
        try {
            $query = Book::query()->with('author');

            if ($data->approved !== null) {
                $query->where('is_approved', $data->approved);
            }

            if ($data->authorId !== null) {
                $query->where('author_id', $data->authorId);
            }

            if ($data->status !== null) {
                $query->where('status', $data->status);
            }

            if ($data->search) {
                $bookIds = $this->searchWithElasticsearch($data->search);

                if (empty($bookIds)) {
                    return new CursorPaginator(
                        items: collect(),
                        perPage: $data->perPage,
                        cursor: null,
                        options: [
                            'path' => request()->url(),
                            'cursorName' => 'cursor',
                            'parameters' => request()->query(),
                        ]
                    );
                }

                $query->whereIn('id', $bookIds);
            }

            $allowedSorts = ['created_at', 'title', 'id'];
            $sortBy = in_array($data->sortBy, $allowedSorts, true) ? $data->sortBy : 'created_at';
            $sortOrder = strtolower($data->sortOrder) === 'asc' ? 'asc' : 'desc';

            $query->orderBy($sortBy, $sortOrder)
                ->orderBy('id', $sortOrder);

            return $query->cursorPaginate(
                perPage: $data->perPage,
                columns: ['*'],
                cursorName: 'cursor',
                cursor: $data->cursor ? Cursor::fromEncoded($data->cursor) : null
            );
        } catch (Throwable $e) {
            throw BookListingException::failed(
                filters: $data->toArray(),
                previous: $e
            );
        }
    }

    private function searchWithElasticsearch(string $search): array
    {
        try {
            $index = config('elasticsearch.index.books', 'books');

            $response = $this->client->search([
                'index' => $index,
                'body'  => [
                    'query' => [
                        'bool' => [
                            'should' => [
                                [
                                    'multi_match' => [
                                        'query'     => $search,
                                        'fields'    => ['title^3', 'description', 'slug^2'],
                                        'fuzziness' => 'AUTO',
                                    ],
                                ],
                                [
                                    'wildcard' => [
                                        'title' => [
                                            'value' => "*{$search}*",
                                            'case_insensitive' => true,
                                        ],
                                    ],
                                ],
                                [
                                    'wildcard' => [
                                        'slug' => [
                                            'value' => "*{$search}*",
                                            'case_insensitive' => true,
                                        ],
                                    ],
                                ],
                            ],
                            'minimum_should_match' => 1,
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

        } catch (Throwable $e) {
            throw BookSearchException::elasticFailed(
                search: $search,
                previous: $e
            );
        }
    }
}