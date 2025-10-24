<?php

namespace App\Modules\User\DTOs;

use App\Models\User;

class ToggleUserStatusResultData
{
    public function __construct(
        public readonly User $user,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'is_active' => $this->user->is_active,
            'role' => $this->user->role,
        ];
    }
}
