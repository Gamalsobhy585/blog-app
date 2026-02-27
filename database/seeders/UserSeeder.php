<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@library.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Admin',
                'password' => Hash::make('123456789'), // Laravel uses argon2id automatically
                'is_active' => true,
            ]
        );

        $admin->syncRoles(['admin']);

        $librarian = User::updateOrCreate(
            ['email' => 'librarian@library.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Librarian',
                'password' => Hash::make('123456789'),
                'is_active' => true,
            ]
        );

        $librarian->syncRoles(['librarian']);

        $member = User::updateOrCreate(
            ['email' => 'user@library.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'User',
                'password' => Hash::make('123456789'),
                'is_active' => true,
            ]
        );

        $member->syncRoles(['member']);
    }
}