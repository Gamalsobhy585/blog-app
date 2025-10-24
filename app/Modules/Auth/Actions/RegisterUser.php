<?php

namespace App\Modules\Auth\Actions;

use App\Models\User;
use App\Modules\Auth\DTOs\RegisterResultData;
use App\Modules\Auth\DTOs\RegisterUserData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;
use App\Modules\Auth\Exceptions\UserRegistrationException;

class RegisterUser
{
    public function execute(RegisterUserData $data): User
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = User::create([
                    'name' => $data->name,
                    'email' => $data->email,
                    'password' => Hash::make($data->password),
                ]);

                event(new Registered($user));

                return new RegisterResultData($user);
            });
        } catch (\Throwable $e) {
            throw UserRegistrationException::failed($e->getMessage());
        }
    }

}