<?php

namespace App\Modules\Auth\DTOs;

use Illuminate\Http\Request;

class RegisterUserData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly int $role,
        public readonly ?string $profile_photo_path = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $profilePhotoPath = null;

        if ($request->hasFile('profile_photo_path')) {
            $profilePhotoPath = $request->file('profile_photo_path')
                ->store('profile-photos', 's3');         }

        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
            role: (int) $request->input('role'),
            profile_photo_path: $profilePhotoPath,
        );
    }
}

 
