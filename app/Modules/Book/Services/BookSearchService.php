<?php

namespace App\Modules\Book\Services;

use App\Models\Book;
use Elastic\Elasticsearch\Client;
use App\Modules\Book\Exceptions\BookSearchSyncException;
use Illuminate\Support\Facades\Log;
use Throwable;

class BookSearchService
{
    public function __construct(private readonly Client $client) {}

    public function index(Book $book): void
    {
        try {
            Log::info('BookSearchService: before index', ['book_id' => $book->id]);

            $result = $this->client->index([
                'index' => config('elasticsearch.index.books', 'books'),
                'id'    => $book->id,
                'body'  => [
                    'id'               => $book->id,
                    'title'            => $book->title,
                    'description'      => $book->description,
                    'slug'             => $book->slug,
                    'status'           => $book->status instanceof \BackedEnum
                                            ? $book->status->value
                                            : $book->status,
                    'is_approved'      => (bool) $book->is_approved,
                    'author_id'        => $book->author_id,
                    'available_copies' => $book->available_copies,
                    'price'            => $book->price,
                    'created_at'       => optional($book->created_at)->toDateTimeString(),
                ],
            ]);

            Log::info('BookSearchService: after index', [
                'book_id' => $book->id,
                'result'  => $result->asArray(),
            ]);

        } catch (Throwable $e) {
            Log::error('BookSearchService: exception caught', [
                'book_id' => $book->id,
                'error'   => $e->getMessage(),
            ]);

            throw BookSearchSyncException::syncFailed(
                bookId: $book->id,
                slug: $book->slug,
                previous: $e
            );
        }
    }
}