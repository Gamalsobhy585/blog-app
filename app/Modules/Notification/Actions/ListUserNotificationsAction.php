<?php

namespace App\Modules\Notifications\Actions;

use App\Models\User;

class ListUserNotificationsAction
{
    public function execute(User $user, int $perPage = 10)
    {
        return $user->notifications()
            ->latest()
            ->paginate($perPage)
            ->through(function ($notification) {
                return [
                    'id'        => $notification->id,
                    'type'      => class_basename($notification->type),
                    'data'      => $notification->data,
                    'read_at'   => $notification->read_at,
                    'created_at'=> $notification->created_at->toDateTimeString(),
                ];
            });
    }
}
