<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

class UserRegistrationException extends Exception
{
    public static function failed(string $message = 'User registration failed.'): self
    {
        return new self($message);
    }
}
