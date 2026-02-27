<?php

namespace App\Modules\Authorization\Actions;

use App\Modules\Authorization\Dtos\AssignUserRoleDto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Models\Role;

class AssignUserRoleAction
{
    public function execute(AssignUserRoleDto $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = User::query()->findOrFail($dto->userId);

            $role = Role::query()
                ->where('name', $dto->roleName)
                ->first();

            if (!$role) {
                throw new ModelNotFoundException("Role not found.");
            }

            $user->syncRoles([$role->name]);

    

            return $user->fresh();
        });
    }
}