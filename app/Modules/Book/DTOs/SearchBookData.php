<?php

namespace App\Modules\Book\DTOs;

use Illuminate\Http\Request;

class SearchBookData
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?bool $approved = null,
        public readonly ?int $authorId = null,
        public readonly ?int $status = null,

        public readonly ?string $cursor = null,
        public readonly int $perPage = 15,

        public readonly string $sortBy = 'created_at',
        public readonly string $sortOrder = 'desc',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->input('search'),
            approved: $request->has('approved') ? $request->boolean('approved') : null,
            authorId: $request->filled('author_id') ? (int) $request->input('author_id') : null,
            status: $request->filled('status') ? (int) $request->input('status') : null,

            cursor: $request->input('cursor'),
            perPage: $request->integer('per_page', 15),

            sortBy: $request->input('sort_by', 'created_at'),
            sortOrder: $request->input('sort_order', 'desc'),
        );
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'approved' => $this->approved,
            'author_id' => $this->authorId,
            'status' => $this->status,
            'cursor' => $this->cursor,
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
            'sort_order' => $this->sortOrder,
        ];
    }
}