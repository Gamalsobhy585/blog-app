<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Actions\RegisterUser;
use App\Modules\Auth\Actions\LoginUser;
use App\Modules\Auth\Actions\LogoutUser;
use App\Modules\Auth\Actions\ResetPassword;
use App\Modules\Auth\DTOs\RegisterUserData;
use App\Modules\Auth\DTOs\LoginUserData;
use App\Modules\Auth\DTOs\ResetPasswordData;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct(
        private readonly LogoutUser $logoutUser,
    ) {}

    public function register(RegisterRequest $request, RegisterUser $action)
    {
        $result = $action->execute(
            RegisterUserData::fromRequest($request)
        );

        return response()->json([
            'message' => 'Registration successful',
            'status'  => 'success',
            'data'    => $result->toArray(),
        ], 201);
    }


    public function login(LoginRequest $request, LoginUser $action) : JsonResponse
    {
        $result = $action->execute(LoginUserData::fromRequest($request->validated()));

        return response()->json([
            'status' => 'success',
            'data' => $result->toArray(),
        ], 200);
    }


    public function logout(): JsonResponse
    {
        $this->logoutUser->execute();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => __($status)
            ], 400);
        }

        return response()->json([
            'message' => __($status)
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPassword $action)
    {
        $result = $action->execute(
            ResetPasswordData::fromRequest($request->validated())
        );

        return response()->json([
            'message' => 'Password has been reset successfully',
            'data' => $result->toArray(),
        ], 200);
    }

}