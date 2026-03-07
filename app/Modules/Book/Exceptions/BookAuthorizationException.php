<?php

namespace App\Modules\Book\Exceptions;

use Throwable;

class BookAuthorizationException extends BookException
{
    public static function notAllowedToCreate(int $userId, ?Throwable $previous = null): self
    {
        return new self(
            message: "User {$userId} is not allowed to create books.",
            context: [
                'user_id' => $userId,
                'module' => 'book',
                'action' => 'create',
                'required_roles' => ['admin', 'librarian'],
            ],
            code: 2001,
            previous: $previous
        );
    }

    public static function notAllowedToApprove(int $userId): self
    {
        return new self(
            message: "User {$userId} is not allowed to approve books.",
            context: [
                'user_id' => $userId,
                'module' => 'book',
                'action' => 'approve',
                'required_role' => 'admin',
            ],
            code: 2002
        );
    }

    public static function notAllowedToReject(int $userId): self
    {
        return new self(
            message: "User {$userId} is not allowed to reject books.",
            context: [
                'user_id' => $userId,
                'module' => 'book',
                'action' => 'reject',
                'required_role' => 'admin',
            ],
            code: 2003
        );
    }
}