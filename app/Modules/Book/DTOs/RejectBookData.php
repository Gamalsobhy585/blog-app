<?php

namespace App\Modules\Book\DTOs;

use Illuminate\Http\Request;

class RejectBookData
{
    public function __construct(
        public readonly string $reason
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            reason: $request->string('reason')->toString()
        );
    }

    public function toArray(): array
    {
        return [
            'reason' => $this->reason,
        ];
    }
}