<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\DTOs\ResetPasswordData;
use App\Modules\Auth\DTOs\ResetPasswordResultData;
use App\Modules\Auth\Exceptions\ResetPasswordException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;


class ResetPassword
{
    public function execute(ResetPasswordData $data): ResetPasswordResultData
    {
        try {
            $status = Password::reset(
                [
                    'email' => $data->email,
                    'password' => $data->password,
                    'token' => $data->token,
                ],
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status !== Password::PASSWORD_RESET) {
                throw ResetPasswordException::failed(__($status));
            }

            $user = User::where('email', $data->email)->first();
            $token = $user->createToken('auth_token')->plainTextToken;

            return new ResetPasswordResultData($user, $token);
        } catch (\Throwable $e) {
            throw ResetPasswordException::failed($e->getMessage());
        }
    }
}
