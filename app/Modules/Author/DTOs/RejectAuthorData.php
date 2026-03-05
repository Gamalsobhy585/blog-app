<?php

namespace App\Modules\Author\DTOs;

use Illuminate\Http\Request;

class RejectAuthorData
{
    public function __construct(
        public readonly string $reason
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(reason: $request->string('reason')->toString());
    }
}