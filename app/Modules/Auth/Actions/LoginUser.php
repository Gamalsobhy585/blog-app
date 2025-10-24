<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\DTOs\LoginUserData;
use App\Modules\Auth\DTOs\LoginResultData;
use App\Modules\Auth\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Auth;

class LoginUser
{
    public function execute(LoginUserData $data): LoginResultData
    {
        if (!Auth::attempt([
            'email' => $data->email,
            'password' => $data->password,
        ])) {
            throw InvalidCredentialsException::create();
        }

        $user = Auth::user();

        $token = $user->createToken($data->device_name ?? 'default_device')->plainTextToken;

        return new LoginResultData($user, $token);
    }
}
