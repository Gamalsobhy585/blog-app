<?php
// UpdateAuthorData.php
namespace App\Modules\Author\DTOs;

use Illuminate\Http\Request;

class UpdateAuthorData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $bio = null,
        public readonly ?string $nationality = null,
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
        return array_filter([
            'name' => $this->name,
            'bio' => $this->bio,
            'nationality' => $this->nationality,
        ], fn($value) => !is_null($value));
    }
}
