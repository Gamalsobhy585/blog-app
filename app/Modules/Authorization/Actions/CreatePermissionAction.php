<?php

namespace App\Modules\Authorization\Actions;

use App\Modules\Authorization\Dtos\CreatePermissionDto;
use Spatie\Permission\Models\Permission;

class CreatePermissionAction
{
    public function execute(CreatePermissionDto $dto): Permission
    {
        return Permission::create([
            'name' => $dto->name,
            'guard_name' => 'web',
        ]);
    }
}