<?php

// CreateAuthorData.php
namespace App\Modules\Author\DTOs;

use Illuminate\Http\Request;

class CreateAuthorData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $bio,
        public readonly ?string $nationality,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            bio: $request->input('bio'),
            nationality: $request->input('nationality'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'bio' => $this->bio,
            'nationality' => $this->nationality,
        ];
    }
}