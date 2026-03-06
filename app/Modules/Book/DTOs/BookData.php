<?php

namespace App\Modules\Book\DTOs;

use App\Models\Book;

class BookData
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $slug,
        public readonly int $totalCopies,
        public readonly int $availableCopies,
        public readonly ?string $price,
        public readonly ?string $coverImage,
        public readonly int $status,
        public readonly bool $isApproved,
        public readonly ?int $authorId,
        public readonly string $createdAt,
    ) {}

    public static function fromModel(Book $book): self
    {
        return new self(
            uuid: $book->uuid,
            title: $book->title,
            description: $book->description,
            slug: $book->slug,
            totalCopies: (int) $book->total_copies,
            availableCopies: (int) $book->available_copies,
            price: $book->price !== null ? (string) $book->price : null,
            coverImage: $book->cover_image,
            status: is_object($book->status) ? $book->status->value : (int) $book->status,
            isApproved: (bool) $book->is_approved,
            authorId: $book->author_id,
            createdAt: $book->created_at?->toDateTimeString() ?? now()->toDateTimeString(),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'total_copies' => $this->totalCopies,
            'available_copies' => $this->availableCopies,
            'price' => $this->price,
            'cover_image' => $this->coverImage,
            'status' => $this->status,
            'is_approved' => $this->isApproved,
            'author_id' => $this->authorId,
            'created_at' => $this->createdAt,
        ];
    }
}