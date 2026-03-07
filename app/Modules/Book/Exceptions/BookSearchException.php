<?php

namespace App\Modules\Book\Exceptions;

use Throwable;

class BookSearchException extends BookException
{
    public static function elasticFailed(
        string $search,
        ?Throwable $previous = null
    ): self {
        return new self(
            message: "Book search failed for keyword '{$search}' using Elasticsearch.",
            context: [
                'module' => 'book',
                'action' => 'search',
                'driver' => 'elasticsearch',
                'search' => $search,
            ],
            code: 4002,
            previous: $previous
        );
    }
}