<?php

namespace App\Modules\Book\Actions;

use App\Models\Book;
use App\Models\User;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookApprovedNotification;
use App\Modules\Book\Exceptions\BookApprovalException;
use App\Modules\Book\Exceptions\BookAuthorizationException;

class ApproveBookAction
{
    public function execute(Book $book, User $admin): Book
    {
        if (! $admin->hasRole('admin')) {
            throw BookAuthorizationException::notAllowedToApprove($admin->id);
        }

        try {
            return DB::transaction(function () use ($book, $admin) {

                $oldValues = [
                    'is_approved' => $book->is_approved,
                    'approval_status' => $book->approval_status,
                    'approved_by' => $book->approved_by,
                    'approved_at' => optional($book->approved_at)?->toDateTimeString(),
                    'rejected_at' => optional($book->rejected_at)?->toDateTimeString(),
                    'rejection_reason' => $book->rejection_reason,
                ];

                $book->update([
                    'is_approved'      => true,
                    'approval_status'  => 1, // better later as enum
                    'approved_by'      => $admin->id,
                    'approved_at'      => now(),
                    'rejected_at'      => null,
                    'rejected_by'      => null,
                    'rejection_reason' => null,
                ]);



                if ($book->created_by) {
                    $creator = User::find($book->created_by);

                    if ($creator) {
                        Notification::send($creator, new BookApprovedNotification($book, $admin));
                    }
                }

                Log::info('Book approved successfully', [
                    'book_id' => $book->id,
                    'book_slug' => $book->slug,
                    'admin_id' => $admin->id,
                ]);

                return $book->fresh();
            });
        } catch (Throwable $e) {
            Log::error('ApproveBookAction failed', [
                'book_id' => $book->id,
                'book_slug' => $book->slug,
                'admin_id' => $admin->id,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw BookApprovalException::failed(
                bookId: $book->id,
                slug: $book->slug,
                adminId: $admin->id,
                previous: $e
            );
        }
    }
}