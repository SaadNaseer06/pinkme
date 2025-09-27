<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Patient;
use App\Models\Application;
use App\Models\SponsorshipProgram;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create patient role
        $patientRole = Role::firstOrCreate(['name' => 'patient']);
        
        // Create sponsorship programs first
        $programs = SponsorshipProgram::factory(10)->create();
        
        // Create 20 patients with complete profiles and applications
        for ($i = 1; $i <= 20; $i++) {
            // Create user
            $user = User::create([
                'email' => "patient{$i}@example.com",
                'password' => Hash::make('password'),
                'role_id' => $patientRole->id,
            ]);
            
            // Create user profile
            UserProfile::create([
                'user_id' => $user->id,
                'full_name' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'date_of_birth' => fake()->dateTimeBetween('-70 years', '-18 years'),
                'gender' => fake()->randomElement(['male', 'female', 'other']),
                'location' => fake()->city() . ', ' . fake()->country(),
            ]);
            
            // Create patient record
            $patient = Patient::create([
                'user_id' => $user->id,
                'marital_status' => fake()->randomElement(['single', 'married', 'divorced', 'widowed']),
                'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
                'diagnosis' => fake()->randomElement([
                    'Breast Cancer',
                    'Lung Cancer',
                    'Colorectal Cancer',
                    'Prostate Cancer',
                    'Skin Cancer',
                    'Leukemia',
                    'Lymphoma',
                    'Brain Tumor'
                ]),
                'diagnosis_date' => fake()->dateTimeBetween('-2 years', 'now'),
                'disease_stage' => fake()->randomElement(['Stage I', 'Stage II', 'Stage III', 'Stage IV']),
                'disease_type' => fake()->randomElement(['Primary', 'Secondary', 'Metastatic']),
                'genetic_test' => fake()->randomElement(['Positive', 'Negative', 'Pending', 'Not Tested']),
            ]);
            
            // Create 1-5 applications for each patient
            $applicationCount = fake()->numberBetween(1, 5);
            for ($j = 0; $j < $applicationCount; $j++) {
                Application::create([
                    'patient_id' => $patient->id,
                    'reviewer_id' => null, // Will be assigned later
                    'program_id' => $programs->random()->id,
                    'title' => fake()->sentence(4),
                    'description' => fake()->paragraph(3),
                    'status' => fake()->randomElement(['Pending', 'Approved', 'Rejected', 'Under Review']),
                    'submission_date' => fake()->dateTimeBetween('-6 months', 'now'),
                    'decision_date' => fake()->optional(0.6)->dateTimeBetween('-3 months', 'now'),
                    'rejection_reason' => fake()->optional(0.2)->sentence(),
                ]);
            }
        }
        
        $this->command->info('Created 20 patients with profiles and applications');
    }
}