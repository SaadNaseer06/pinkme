<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => 1, // Admin
            ],
            [
                'email' => 'sponsor@example.com',
                'password' => Hash::make('password'),
                'role_id' => 2, // Sponsor
            ],
            [
                'email' => 'patient@example.com',
                'password' => Hash::make('password'),
                'role_id' => 3, // Patient
            ],
            [
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'role_id' => 4, // Case Manager
            ],
            // Additional Case Managers
            [
                'email' => 'case_manager1@example.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
            ],
            [
                'email' => 'case_manager2@example.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
            ],
            [
                'email' => 'case_manager3@example.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'password' => $userData['password'],
                    'role_id' => $userData['role_id'],
                ]
            );

            UserProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name' => ucfirst(explode('@', $userData['email'])[0]),
                    'phone' => '555-123-' . rand(1000, 9999),
                    'date_of_birth' => now()->subYears(rand(25, 60)),
                    'gender' => 'other',
                ]
            );
        }
    }
}
