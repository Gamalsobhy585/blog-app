<?php

namespace App\Modules\Book\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Modules\Book\Exceptions\BookCoverUploadException;
use Throwable;

class BookCoverUploader
{
    public function upload(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        try {
            return Storage::disk('s3')->putFile('books/covers', $file);
        } catch (Throwable $e) {
            throw BookCoverUploadException::failed($e->getMessage());
        }
    }
}