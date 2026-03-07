<?php

// SendBookApprovalNotification.php
namespace App\Modules\Book\Listeners;

use App\Models\User;
use App\Modules\Book\Events\BookPendingApproval;
use App\Modules\Book\Notifications\BookPendingApprovalNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookApprovalNotification implements ShouldQueue
{
    public function handle(BookPendingApproval $event): void
    {
        // Get all admin users
        $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->where('is_active', true)
            ->get();

        // Send notification to each admin
        foreach ($admins as $admin) {
            $admin->notify(new BookPendingApprovalNotification(
                $event->book,
                $event->createdBy
            ));
        }
    }
}
