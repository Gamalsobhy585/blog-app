<?php
namespace App\Modules\Author\DTOs;

use Illuminate\Http\Request;

class SearchAuthorData
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $nationality = null,
        public readonly ?bool $approved = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly string $sortBy = 'created_at',
        public readonly string $sortOrder = 'desc',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->input('search'),
            nationality: $request->input('nationality'),
            approved: $request->boolean('approved', null),
            page: $request->integer('page', 1),
            perPage: $request->integer('per_page', 15),
            sortBy: $request->input('sort_by', 'created_at'),
            sortOrder: $request->input('sort_order', 'desc'),
        );
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'nationality' => $this->nationality,
            'approved' => $this->approved,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
            'sort_order' => $this->sortOrder,
        ];
    }
}
