<?php

namespace App\Modules\Auth\Exceptions;

use Illuminate\Validation\ValidationException;

class InvalidCredentialsException extends ValidationException
{
    public static function create(): self
    {
        return self::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
}
