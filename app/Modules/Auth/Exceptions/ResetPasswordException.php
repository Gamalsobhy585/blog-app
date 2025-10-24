<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

class ResetPasswordException extends Exception
{
    public static function failed(string $message): self
    {
        return new self("Password reset failed: {$message}");
    }
}
