<?php

namespace App\Modules\Authorization\Dtos;

final class CreateRoleDto
{
    public function __construct(
        public readonly string $name
    ) {}

    public static function from(array $validated): self
    {
        return new self(
            name: $validated['name']
        );
    }
}