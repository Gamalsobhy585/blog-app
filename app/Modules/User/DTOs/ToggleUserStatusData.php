<?php

namespace App\Modules\User\DTOs;

class ToggleUserStatusData
{
    public function __construct(
        public readonly int $user_id,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            user_id: $data['user_id'],
        );
    }
}
