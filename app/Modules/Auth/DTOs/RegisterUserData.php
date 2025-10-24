<?php

namespace App\Modules\Auth\DTOs;

class RegisterUserData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
        );
    }

 
}