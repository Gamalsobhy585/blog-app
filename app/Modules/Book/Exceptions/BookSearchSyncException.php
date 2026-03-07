<?php

namespace App\Modules\Book\Exceptions;

use Throwable;

class BookSearchSyncException extends BookException
{
    public static function syncFailed(
        int $bookId,
        string $slug,
        ?Throwable $previous = null
    ): self {

        return new self(
            message: "Failed to sync book '{$slug}' with Elasticsearch.",
            context: [
                'book_id' => $bookId,
                'slug' => $slug,
                'search_engine' => 'elasticsearch',
                'module' => 'book',
                'action' => 'search_sync',
            ],
            code: 1003,
            previous: $previous
        );
    }
}