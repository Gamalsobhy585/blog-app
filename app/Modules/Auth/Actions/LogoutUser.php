<?php

namespace App\Modules\Auth\Actions;

use Illuminate\Support\Facades\Auth;

class LogoutUser
{
    public function execute(): void
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }
    }
}
