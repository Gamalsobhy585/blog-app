<?php

namespace App\Modules\Authorization\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Authorization\Actions\AssignUserRoleAction;
use App\Modules\Authorization\Dtos\AssignUserRoleDto;
use App\Modules\Authorization\Requests\AssignUserRoleRequest;

class UserRolesController extends Controller
{
    public function store(AssignUserRoleRequest $request, User $user, AssignUserRoleAction $action)
    {
        $dto = AssignUserRoleDto::from($user->id, $request->validated());

        $updatedUser = $action->execute($dto);

        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully.',
            'data' => [
                'user_id' => $updatedUser->id,
                'roles' => $updatedUser->getRoleNames(),
            ],
        ]);
    }
}