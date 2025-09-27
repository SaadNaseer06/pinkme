<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SponsorshipProgramFactory extends Factory
{
    public function definition(): array
    {
        $goalAmount = $this->faker->numberBetween(10000, 500000);
        $raisedAmount = $this->faker->numberBetween(0, $goalAmount);
        
        return [
            'title' => $this->faker->randomElement([
                'Cancer Treatment Support Program',
                'Medical Equipment Fund',
                'Patient Care Assistance',
                'Emergency Medical Aid',
                'Chemotherapy Support Fund',
                'Radiation Therapy Program',
                'Surgical Assistance Fund',
                'Medication Support Program'
            ]),
            'description' => $this->faker->paragraph(4),
            'goal_amount' => $goalAmount,
            'raised_amount' => $raisedAmount,
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->optional(0.7)->dateTimeBetween('now', '+2 years'),
        ];
    }
}