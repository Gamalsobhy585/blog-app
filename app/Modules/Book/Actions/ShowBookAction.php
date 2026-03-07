<?php

namespace App\Modules\Book\Actions;

use App\Models\Book;
use Throwable;
use App\Modules\Book\Exceptions\BookShowException;

class ShowBookAction
{
    public function execute(Book $book): Book
    {
        try {
            return $book->loadMissing('author');
        } catch (Throwable $e) {
            throw BookShowException::failed(
                slug: $book->slug,
                previous: $e
            );
        }
    }
}