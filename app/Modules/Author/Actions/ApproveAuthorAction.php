<?php

namespace App\Modules\Author\Actions;

use App\Models\Author;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AuthorApprovedNotification;

class ApproveAuthorAction
{
    public function execute(Author $author, User $admin): Author
    {
        return DB::transaction(function () use ($author, $admin) {

            $author->update([
                'is_approved'     => true,
                'approval_status' => '1',// 1 = approved
                'approved_by'     => $admin->id,
                'approved_at'     => now(),
                'rejected_at'     => null,
                'rejection_reason'=> null,
            ]);

            // notify creator
            if ($author->created_by) {
                $creator = User::find($author->created_by);
                if ($creator) {
                    Notification::send($creator, new AuthorApprovedNotification($author, $admin));
                }
            }

            return $author->fresh();
        });
    }
}