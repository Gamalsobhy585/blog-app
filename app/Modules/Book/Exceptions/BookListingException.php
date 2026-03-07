<?php

namespace App\Modules\Book\Exceptions;

use Throwable;

class BookListingException extends BookException
{
    public static function failed(
        array $filters = [],
        ?Throwable $previous = null
    ): self {
        return new self(
            message: 'Failed to list books.',
            context: [
                'module' => 'book',
                'action' => 'list',
                'filters' => $filters,
            ],
            code: 4001,
            previous: $previous
        );
    }
}