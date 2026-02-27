<?php

namespace App\Modules\Authorization\Actions;

use App\Modules\Authorization\Dtos\SyncRolePermissionsDto;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class SyncRolePermissionsAction
{
    public function execute(SyncRolePermissionsDto $dto): Role
    {
        return DB::transaction(function () use ($dto) {
            $role = Role::query()->findOrFail($dto->roleId);

            // This replaces all role permissions with the given list
            $role->syncPermissions($dto->permissions);

            return $role->fresh();
        });
    }
}