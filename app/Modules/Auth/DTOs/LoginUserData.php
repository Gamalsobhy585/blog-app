<?php

namespace App\Modules\Auth\DTOs;

class LoginUserData
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public ?string $device_name = null,
        public ?string $device_token = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            device_name: $data['device_name'] ?? null,
            device_token: $data['device_token'] ?? null,
        );
    }

  
}