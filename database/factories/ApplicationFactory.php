<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use App\Models\SponsorshipProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'reviewer_id' => User::factory(),
            'program_id' => SponsorshipProgram::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement(['Pending', 'Approved', 'Rejected', 'Under Review']),
            'submission_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'decision_date' => $this->faker->optional(0.6)->dateTimeBetween('-3 months', 'now'),
            'rejection_reason' => $this->faker->optional(0.2)->sentence(),
        ];
    }
}