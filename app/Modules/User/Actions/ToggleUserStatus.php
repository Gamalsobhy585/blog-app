<?php

namespace App\Modules\User\Actions;

use App\Models\User;
use App\Modules\User\DTOs\ToggleUserStatusData;
use App\Modules\User\DTOs\ToggleUserStatusResultData;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ToggleUserStatus
{
    public function execute(ToggleUserStatusData $data): ToggleUserStatusResultData
    {
        return DB::transaction(function () use ($data) {
            $user = User::findOrFail($data->user_id);

            $user->is_active = !$user->is_active;
            $user->save();

            return new ToggleUserStatusResultData($user);
        });
    }
}
