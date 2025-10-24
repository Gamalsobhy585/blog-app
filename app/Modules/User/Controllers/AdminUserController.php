<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Actions\ToggleUserStatus;
use App\Modules\User\DTOs\ToggleUserStatusData;
use App\Modules\User\Actions\GetUsers;
use App\Modules\User\DTOs\FilterUsersData;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function toggleStatus(Request $request, ToggleUserStatus $action)
    {
        $data = ToggleUserStatusData::fromRequest($request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]));

        $result = $action->execute($data);

        return response()->json([
            'message' => $result->user->is_active 
                ? 'User activated successfully.' 
                : 'User deactivated successfully.',
            'data' => $result->toArray(),
        ]);
    }
    public function index(Request $request, GetUsers $action)
    {
        $data = FilterUsersData::fromRequest($request->all());
        $users = $action->execute($data);

        return response()->json([
            'status' => 'success',
            'count' => $users->count(),
            'data' => $users,
        ]);
    }
}
