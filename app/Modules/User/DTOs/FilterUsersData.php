<?php

namespace App\Modules\User\DTOs;

class FilterUsersData
{
    public function __construct(
        public readonly string $status = 'all', 
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            status: $data['status'] ?? 'all',
        );
    }
}
