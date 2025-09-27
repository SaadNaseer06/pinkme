<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Get the admin role
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            throw new \Exception('Admin role not found. Please run RoleSeeder first.');
        }

        // Create admin user
        User::create([
            'email' => 'admin@pinkme.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
        ]);
    }
} 