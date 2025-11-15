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

        public function execute(RegisterUserData $data): RegisterResultData
        {
            try {
                return DB::transaction(function () use ($data) {
                    $user = User::create([
                        'name' => $data->name,
                        'email' => $data->email,
                      'password' => password_hash($data->password, PASSWORD_ARGON2ID, [
                            'memory_cost' => 1024,
                            'time_cost' => 2,
                            'threads' => 2,
                        ]),
                        'role' => $data->role,
                        'profile_photo_path' => $data->profile_photo_path,
                        'is_active' => true,
                        


                    ]);

                    event(new Registered($user));

                    return new RegisterResultData($user);
                });
            } catch (\Throwable $e) {
                throw UserRegistrationException::failed($e->getMessage());
            }
        }
    }
