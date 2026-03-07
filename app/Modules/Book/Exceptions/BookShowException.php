<?php

namespace App\Modules\Book\Exceptions;

use Throwable;

class BookShowException extends BookException
{
    public static function failed(string $slug, ?Throwable $previous = null): self
    {
        return new self(
            message: "Failed to load book '{$slug}'.",
            context: [
                'module' => 'book',
                'action' => 'show',
                'slug' => $slug,
            ],
            code: 5001,
            previous: $previous
        );
    }
}