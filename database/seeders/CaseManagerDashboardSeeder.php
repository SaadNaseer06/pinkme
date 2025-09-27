<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Patient;
use App\Models\SponsorshipProgram;
use App\Models\Application;

/**
 * Seeder used to populate the database with case managers
 * and a set of applications assigned to one of them. This makes
 * it easy to view a realistic dashboard in development.
 */
class CaseManagerDashboardSeeder extends Seeder
{
    public function run(): void
    {
        // Create 4 case manager users
        $caseManagers = collect();

        for ($i = 1; $i <= 4; $i++) {
            $user = User::factory()->create([
                'role_id'  => 4,
                'email'    => "case_manager{$i}@example.com",
                'password' => bcrypt('password'),
            ]);

            // Create profile for each case manager
            UserProfile::factory()->create([
                'user_id' => $user->id,
            ]);

            $caseManagers->push($user);
        }

        // Create some sponsorship programs
        $programs = SponsorshipProgram::factory()->count(3)->create();

        // Create a set of patients
        $patients = Patient::factory()->count(10)->create();

        // Create applications assigned randomly to case managers
        $statuses = ['Pending', 'Under Review', 'Approved', 'Rejected'];
        Application::factory()->count(20)->make()->each(function ($application) use ($caseManagers, $programs, $patients, $statuses) {
            $application->reviewer_id = $caseManagers->random()->id;
            $application->program_id  = $programs->random()->id;
            $application->patient_id  = $patients->random()->id;
            $application->status      = $statuses[array_rand($statuses)];
            $application->save();
        });
    }
}
