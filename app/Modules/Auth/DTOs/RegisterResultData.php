<?php

namespace App\Modules\Auth\DTOs;

use App\Models\User;

class RegisterResultData
{
    public function __construct(
        public readonly User $user,
    ) {}

    public function toArray(): array
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role'  => $this->user->role_name,
                'is_active' => $this->user->active_string,
                'profile_photo_url'  => $this->user->profile_photo_url,
                'created_at' => $this->user->created_at,
            ],
        ];
    }
}
