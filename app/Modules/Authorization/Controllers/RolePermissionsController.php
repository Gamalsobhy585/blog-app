<?php

namespace App\Modules\Authorization\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authorization\Actions\SyncRolePermissionsAction;
use App\Modules\Authorization\Dtos\SyncRolePermissionsDto;
use App\Modules\Authorization\Requests\SyncRolePermissionsRequest;
use Spatie\Permission\Models\Role;

class RolePermissionsController extends Controller
{
    public function sync(
        SyncRolePermissionsRequest $request,
        Role $role,
        SyncRolePermissionsAction $action
    ) {
        $dto = SyncRolePermissionsDto::from($role->id, $request->validated());

        $updatedRole = $action->execute($dto);

        return response()->json([
            'success' => true,
            'message' => 'Role permissions updated successfully.',
            'data' => [
                'role' => $updatedRole->name,
                'permissions' => $updatedRole->permissions()->pluck('name'),
            ],
        ]);
    }
}