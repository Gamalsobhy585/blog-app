<?php

namespace App\Modules\Authorization\Actions;

use App\Modules\Authorization\Dtos\CreateRoleDto;
use Spatie\Permission\Models\Role;

class CreateRoleAction
{
    public function execute(CreateRoleDto $dto): Role
    {
        return Role::create([
            'name' => $dto->name,
            'guard_name' => 'web', // match your auth guard
        ]);
    }
}