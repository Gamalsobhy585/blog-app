<?php

namespace App\Modules\Authorization\Dtos;

final class SyncRolePermissionsDto
{
    public function __construct(
        public readonly int $roleId,
        /** @var string[] */
        public readonly array $permissions
    ) {}

    public static function from(int $roleId, array $validated): self
    {
        return new self(
            roleId: $roleId,
            permissions: $validated['permissions']
        );
    }
}