<?php

namespace App\Modules\Authorization\Actions;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class ListPermissionsAction
{
    public function execute(): Collection
    {
        return Permission::query()
            ->orderBy('name')
            ->get();
    }
}