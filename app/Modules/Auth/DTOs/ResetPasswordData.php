<?php

namespace App\Modules\Auth\DTOs;

class ResetPasswordData
{
    public function __construct(
        public readonly string $email,
        public readonly string $token,
        public readonly string $password,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            email: $data['email'],
            token: $data['token'],
            password: $data['password'],
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'token' => $this->token,
            'password' => $this->password,
        ];
    }
}