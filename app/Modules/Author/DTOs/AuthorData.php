<?php
namespace App\Modules\Author\DTOs;

use App\Models\Author;

class AuthorData
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $name,
        public readonly ?string $bio,
        public readonly ?string $nationality,
        public readonly bool $isApproved,
        public readonly string $createdAt,
    ) {}

    public static function fromModel(Author $author): self
    {
        return new self(
            uuid: $author->uuid,
            name: $author->name,
            bio: $author->bio,
            nationality: $author->nationality,
            isApproved: $author->is_approved,
            createdAt: $author->created_at->toDateTimeString(),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'bio' => $this->bio,
            'nationality' => $this->nationality,
            'is_approved' => $this->isApproved,
            'created_at' => $this->createdAt,
        ];
    }
}
