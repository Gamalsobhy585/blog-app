<?php

namespace App\Modules\Book\Actions;

use App\Jobs\SyncBookToSearchJob;
use App\Models\Book;
use App\Models\User;
use App\Modules\Book\DTOs\CreateBookData;
use App\Modules\Book\Exceptions\BookCreationException;
use App\Modules\Book\Services\BookCoverUploader;
use App\Notifications\BookPendingApprovalNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class CreateBookAction
{
    public function __construct(
        protected BookCoverUploader $bookCoverUploader
    ) {
    }

    public function execute(CreateBookData $data, User $user): Book
    {
        try {
            return DB::transaction(function () use ($data, $user) {

                $isAdmin = $user->hasRole('admin');

                Log::info('CreateBookAction started', [
                    'user_id' => $user->id,
                    'is_admin' => $isAdmin,
                    'book_title' => $data->title,
                ]);

                $coverImagePath = $this->bookCoverUploader->upload($data->coverImage);

                $book = Book::create([
                    'title'            => $data->title,
                    'description'      => $data->description,
                    'slug'             => $data->slug,
                    'total_copies'     => $data->totalCopies,
                    'available_copies' => $data->availableCopies,
                    'price'            => $data->price,
                    'cover_image'      => $coverImagePath,
                    'status'           => $data->status,
                    'author_id'        => $data->authorId,

                    'is_approved'      => $isAdmin ? 1 : 0,
                    'approval_status'  => $isAdmin ? 1 : 2,
                    'created_by'       => $user->id,
                    'approved_by'      => $isAdmin ? $user->id : null,
                    'approved_at'      => $isAdmin ? now() : null,
                ]);

                Log::info('Book created successfully', [
                    'book_id' => $book->id,
                    'slug' => $book->slug,
                    'approval_status' => $book->approval_status,
                ]);

                if (! $isAdmin) {
                    $admins = User::role('admin')->get();

                    Notification::send(
                        $admins,
                        new BookPendingApprovalNotification($book, $user)
                    );

                    Log::info('Pending approval notification sent to admins', [
                        'book_id' => $book->id,
                        'admin_count' => $admins->count(),
                    ]);
                }

                SyncBookToSearchJob::dispatch($book)->afterCommit();

                return $book->fresh();
            });
        } catch (Throwable $e) {
            Log::error('CreateBookAction failed', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => $user->id,
                'book_title' => $data->title,
            ]);

            throw BookCreationException::failed($e->getMessage());
        }
    }
}