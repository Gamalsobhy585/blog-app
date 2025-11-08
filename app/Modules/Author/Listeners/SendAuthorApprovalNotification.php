<?php

// SendAuthorApprovalNotification.php
namespace App\Modules\Author\Listeners;

use App\Models\User;
use App\Modules\Author\Events\AuthorPendingApproval;
use App\Modules\Author\Notifications\AuthorPendingApprovalNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAuthorApprovalNotification implements ShouldQueue
{
    public function handle(AuthorPendingApproval $event): void
    {
        // Get all admin users
        $admins = User::where('role', 1)
            ->where('is_active', true)
            ->get();

        // Send notification to each admin
        foreach ($admins as $admin) {
            $admin->notify(new AuthorPendingApprovalNotification(
                $event->author,
                $event->createdBy
            ));
        }
    }
}
