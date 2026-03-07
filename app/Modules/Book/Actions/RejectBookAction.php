<?php

namespace App\Modules\Book\Actions;

use App\Models\Book;
use App\Models\User;
use App\Modules\Book\DTOs\RejectBookData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookRejectedNotification;

class RejectBookAction
{
    public function execute(Book $book, RejectBookData $data, User $admin): Book
    {
        return DB::transaction(function () use ($book, $data, $admin) {

            $book->update([
                'is_approved'      => false,
                'approval_status'  => 'rejected',
                'rejected_at'      => now(),
                'rejection_reason' => $data->reason,
            ]);

            // notify creator
            if ($book->created_by) {
                $creator = User::find($book->created_by);
                if ($creator) {
                    Notification::send($creator, new BookRejectedNotification($book, $admin, $data->reason));
                }
            }

            return $book->fresh();
        });
    }
}