<?php

namespace App\Modules\Auth\DTOs;

use App\Models\User;

class LoginResultData
{
    public function __construct(
        public readonly User $user,
        public readonly string $token,
    ) {}

    public function toArray(): array
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'created_at' => $this->user->created_at,
                
            ],
            'token' => $this->token,
        ];
    }
}
