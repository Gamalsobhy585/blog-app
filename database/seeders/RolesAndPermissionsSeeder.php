<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'authors' => [
                'viewAny', 'view', 'create', 'update', 'delete',
                'approve', 'reject',
            ],
            'books' => [
                'viewAny', 'view', 'create', 'update', 'delete',
            ],
            'borrowing' => [
                'request', 'viewAny', 'view',
                'approve', 'reject', 'return',
            ],
            'admin' => [
                'users.manage',
                'roles.manage',
                'permissions.view',
                'stats.view',
            ],
        ];

        $allPermissions = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $perm = str_contains($action, '.') ? $action : "{$module}.{$action}";
                $allPermissions[] = $perm;
                Permission::firstOrCreate(['name' => $perm]);
            }
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $librarian = Role::firstOrCreate(['name' => 'librarian']);
        $member = Role::firstOrCreate(['name' => 'member']);

        // Admin = everything
        $admin->syncPermissions($allPermissions);

        // Librarian permissions (module-based)
        $librarian->syncPermissions([
            // Authors
            'authors.viewAny','authors.view','authors.create','authors.update','authors.approve','authors.reject',

            // Books
            'books.viewAny','books.view','books.create','books.update','books.delete',

            // Borrowing (operate approvals + return)
            'borrowing.viewAny','borrowing.view','borrowing.approve','borrowing.reject','borrowing.return',

            // Admin stats only (optional)
            'stats.view',
        ]);

        // Member permissions
        $member->syncPermissions([
            'authors.viewAny','authors.view','authors.create',
            'books.viewAny','books.view',
            'borrowing.request','borrowing.viewAny','borrowing.view',
        ]);
    }
}