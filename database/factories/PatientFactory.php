<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'blood_group' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'diagnosis' => $this->faker->randomElement([
                'Breast Cancer',
                'Lung Cancer',
                'Colorectal Cancer',
                'Prostate Cancer',
                'Skin Cancer',
                'Leukemia',
                'Lymphoma',
                'Brain Tumor'
            ]),
            'diagnosis_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'disease_stage' => $this->faker->randomElement(['Stage I', 'Stage II', 'Stage III', 'Stage IV']),
            'disease_type' => $this->faker->randomElement(['Primary', 'Secondary', 'Metastatic']),
            'genetic_test' => $this->faker->randomElement(['Positive', 'Negative', 'Pending', 'Not Tested']),
        ];
    }
}