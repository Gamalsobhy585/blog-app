<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'uuid' => Str::uuid(),
            'name' => 'admin',
            'email' => 'Admin@library.com',
            'password' => Hash::make('123456789', [
                'memory' => 1024,       
                'time' => 2,           
                'threads' => 2,        
                'type' => PASSWORD_ARGON2ID
            ]),
            'role' => 1, // 1=admin
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
