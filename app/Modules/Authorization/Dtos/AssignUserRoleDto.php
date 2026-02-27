<?php

namespace App\Modules\Authorization\Dtos;

final class AssignUserRoleDto
{
    public function __construct(
        public readonly int $userId,
        public readonly string $roleName
    ) {}

    public static function from(int $userId, array $validated): self
    {
        return new self(
            userId: $userId,
            roleName: $validated['role']
        );
    }
}