<?php

namespace App\Modules\Book\Exceptions;

use Throwable;

class BookCoverUploadException extends BookException
{
    public static function uploadFailed(
        string $fileName,
        int $userId,
        ?Throwable $previous = null
    ): self {

        return new self(
            message: "Failed to upload book cover '{$fileName}' to S3.",
            context: [
                'file_name' => $fileName,
                'user_id' => $userId,
                'storage' => 'aws-s3',
                'module' => 'book',
                'action' => 'cover_upload',
            ],
            code: 1002,
            previous: $previous
        );
    }
}