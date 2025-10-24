<?php

namespace App\Modules\User\Actions;

use App\Models\User;
use App\Modules\User\DTOs\FilterUsersData;

class GetUsers
{
    public function execute(FilterUsersData $data)
    {
        $query = User::query();

        // Apply local scope dynamically
        match ($data->status) {
            'active' => $query->active(),
            'inactive' => $query->inactive(),
            default => $query, // all
        };

        return $query->latest()->get(['id', 'name', 'email', 'is_active', 'role']);
    }
}
