<?php

namespace App\Modules\Authorization\Actions;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class ListRolesAction
{
    public function execute(): Collection
    {
        return Role::query()
            ->orderBy('name')
            ->get();
    }
}