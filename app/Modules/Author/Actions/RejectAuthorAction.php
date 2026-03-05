<?php

namespace App\Modules\Author\Actions;

use App\Models\Author;
use App\Models\User;
use App\Modules\Author\DTOs\RejectAuthorData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AuthorRejectedNotification;

class RejectAuthorAction
{
    public function execute(Author $author, RejectAuthorData $data, User $admin): Author
    {
        return DB::transaction(function () use ($author, $data, $admin) {

            $author->update([
                'is_approved'      => false,
                'approval_status'  => 'rejected',
                'rejected_at'      => now(),
                'rejection_reason' => $data->reason,
            ]);

            // notify creator
            if ($author->created_by) {
                $creator = User::find($author->created_by);
                if ($creator) {
                    Notification::send($creator, new AuthorRejectedNotification($author, $admin, $data->reason));
                }
            }

            return $author->fresh();
        });
    }
}