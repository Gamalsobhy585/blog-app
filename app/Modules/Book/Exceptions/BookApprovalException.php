<?php

namespace App\Modules\Book\Exceptions;

use Throwable;

class BookApprovalException extends BookException
{
    public static function failed(
        int $bookId,
        string $slug,
        int $adminId,
        ?Throwable $previous = null
    ): self {
        return new self(
            message: "Approval failed for book '{$slug}' by admin {$adminId}.",
            context: [
                'book_id' => $bookId,
                'slug' => $slug,
                'admin_id' => $adminId,
                'module' => 'book',
                'action' => 'approve',
            ],
            code: 3001,
            previous: $previous
        );
    }
}