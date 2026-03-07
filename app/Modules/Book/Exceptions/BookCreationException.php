<?php

namespace App\Modules\Book\Exceptions;

use Throwable;

class BookCreationException extends BookException
{
    public static function failed(
        string $title,
        int $userId,
        ?Throwable $previous = null
    ): self {

        return new self(
            message: "Book creation failed for '{$title}'.",
            context: [
                'book_title' => $title,
                'user_id' => $userId,
                'module' => 'book',
                'action' => 'create',
            ],
            code: 1001,
            previous: $previous
        );
    }
}