<?php

namespace App\Modules\Book\DTOs;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CreateBookData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $slug,
        public readonly int $totalCopies,
        public readonly int $availableCopies,
        public readonly ?string $price,
        public readonly ?UploadedFile $coverImage,
        public readonly int $status,
        public readonly ?int $authorId,
        public readonly bool $isApproved = false,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->string('title')->toString(),
            description: $request->filled('description') ? $request->string('description')->toString() : null,
            slug: $request->filled('slug') ? $request->string('slug')->toString() : null,
            totalCopies: $request->integer('total_copies', 0),
            availableCopies: $request->integer('available_copies', 0),
            price: $request->filled('price') ? (string) $request->input('price') : null,
            coverImage: $request->file('cover_image'),
            status: $request->integer('status', 0),
            authorId: $request->filled('author_id') ? (int) $request->input('author_id') : null,
            isApproved: $request->boolean('is_approved', false),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'total_copies' => $this->totalCopies,
            'available_copies' => $this->availableCopies,
            'price' => $this->price,
            'status' => $this->status,
            'author_id' => $this->authorId,
            'is_approved' => $this->isApproved,
        ];
    }
}