<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'role_id' => Role::inRandomOrder()->first()->id ?? 1,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}
