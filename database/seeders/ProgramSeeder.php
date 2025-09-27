<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Program::query()->delete();

        Program::insert([
            [
                'title' => 'Breast Cancer Awareness',
                'description' => 'A nonprofit initiative supporting women battling breast cancer, raising awareness about early detection and survivorship.',
                'event_date' => '2025-03-30',
                'event_time' => '10:00:00',
                'banner' => 'https://pinkme.testserverwebsite.com/images/program-details.png',
                'status' => 'upcoming',
                'program_fund' => 75000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => "Hope for Her: Women's Health Drive",
                'description' => 'Providing free mammograms, screenings, and counseling for women in need.',
                'event_date' => '2025-03-28',
                'event_time' => '11:00:00',
                'banner' => 'https://pinkme.testserverwebsite.com/images/program-details.png',
                'status' => 'upcoming',
                'program_fund' => 52000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Stronger Together: Cancer Support Group',
                'description' => 'A community-driven program offering emotional and financial support for cancer patients and survivors.',
                'event_date' => '2025-03-28',
                'event_time' => '13:00:00',
                'banner' => 'https://pinkme.testserverwebsite.com/images/program-details.png',
                'status' => 'ongoing',
                'program_fund' => 46500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => "Survivor's Strength: Post-Cancer Rehab",
                'description' => 'A rehabilitation program for women recovering from breast cancer treatment.',
                'event_date' => '2025-03-28',
                'event_time' => '14:30:00',
                'banner' => 'https://pinkme.testserverwebsite.com/images/program-details.png',
                'status' => 'ongoing',
                'program_fund' => 38800,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Screen & Save: Early Detection Program',
                'description' => 'Free breast cancer screening and education workshops for women at risk.',
                'event_date' => '2025-03-28',
                'event_time' => '09:00:00',
                'banner' => 'https://pinkme.testserverwebsite.com/images/program-details.png',
                'status' => 'ongoing',
                'program_fund' => 61200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}