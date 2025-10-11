<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventDate = fake()->dateTimeBetween('now', '+6 months');
        $registrationDeadline = Carbon::parse($eventDate)->subDays(rand(7, 30));
        
        $eventTitles = [
            'Women\'s Health Awareness Campaign',
            'Breast Cancer Research Fundraiser',
            'Annual Pink Ribbon Gala',
            'Women\'s Wellness Workshop',
            'Healthcare Innovation Summit',
            'Pink Walk for a Cause',
            'Women\'s Health Education Seminar',
            'Maternal Health Support Drive',
            'Digital Health Conference',
            'Community Health Fair'
        ];
        
        $locations = [
            'New York Convention Center, NY',
            'Los Angeles Medical Center, CA',
            'Chicago Women\'s Hospital, IL',
            'Houston Community Center, TX',
            'Phoenix Healthcare Plaza, AZ',
            'Philadelphia Medical District, PA',
            'San Antonio Conference Hall, TX',
            'San Diego Wellness Center, CA',
            'Dallas Health Campus, TX',
            'San Jose Innovation Hub, CA'
        ];
        
        $descriptions = [
            'Join us for an impactful event focused on advancing women\'s health through education, awareness, and community support. Together, we can make a difference in women\'s healthcare outcomes.',
            'A comprehensive program designed to bring together healthcare professionals, researchers, and advocates to address critical women\'s health issues and promote innovative solutions.',
            'An exclusive gathering of industry leaders, medical experts, and community champions working towards better healthcare access and outcomes for women everywhere.',
            'Experience a day of learning, networking, and inspiration as we explore the latest advances in women\'s health and discuss strategies for improving care delivery.',
            'Connect with like-minded individuals passionate about women\'s health while supporting vital research and community health initiatives that save lives.'
        ];
        
        $highlights = [
            'Keynote speakers from leading medical institutions • Interactive workshops and panel discussions • Networking opportunities with healthcare professionals • Resource fair with health screenings',
            'Expert-led sessions on latest research • Hands-on wellness activities • Community resource booths • Live entertainment and refreshments • Networking reception',
            'Award ceremony recognizing health champions • Educational workshops for all ages • Free health screenings and consultations • Silent auction fundraiser • Professional networking',
            'Medical expert presentations • Interactive health demonstrations • One-on-one consultations available • Community partner exhibitions • Wellness activity stations',
            'Industry leadership panel • Innovation showcase • Professional development sessions • Community impact presentations • Collaborative networking opportunities'
        ];
        
        return [
            'title' => fake()->randomElement($eventTitles),
            'description' => fake()->randomElement($descriptions),
            'date' => $eventDate,
            'location' => fake()->randomElement($locations),
            'funding_goal' => fake()->randomFloat(2, 5000, 100000), // $5K to $100K
            'status' => fake()->randomElement(['upcoming', 'upcoming', 'upcoming', 'ongoing', 'completed']), // Weight towards upcoming
            'event_highlights' => fake()->randomElement($highlights),
            'image' => 'events/event-' . rand(1, 10) . '.jpg', // Placeholder image paths
            'max_sponsors' => fake()->numberBetween(10, 50),
            'registration_deadline' => $registrationDeadline,
        ];
    }
    
    /**
     * Create an event that is currently open for registration
     */
    public function openForRegistration(): static
    {
        return $this->state(function (array $attributes) {
            $eventDate = fake()->dateTimeBetween('+1 month', '+6 months');
            $registrationDeadline = Carbon::parse($eventDate)->subDays(rand(14, 45));
            
            return [
                'date' => $eventDate,
                'registration_deadline' => $registrationDeadline,
                'status' => 'upcoming',
                'funding_goal' => fake()->randomFloat(2, 10000, 75000),
            ];
        });
    }
    
    /**
     * Create an event that is fully funded
     */
    public function fullyFunded(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'funding_goal' => fake()->randomFloat(2, 5000, 25000),
                'status' => 'upcoming',
            ];
        });
    }
    
    /**
     * Create a past event
     */
    public function past(): static
    {
        return $this->state(function (array $attributes) {
            $eventDate = fake()->dateTimeBetween('-6 months', '-1 month');
            $registrationDeadline = Carbon::parse($eventDate)->subDays(rand(14, 30));
            
            return [
                'date' => $eventDate,
                'registration_deadline' => $registrationDeadline,
                'status' => fake()->randomElement(['completed', 'completed', 'cancelled']),
            ];
        });
    }
    
    /**
     * Create a high-value event
     */
    public function highValue(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'funding_goal' => fake()->randomFloat(2, 50000, 200000),
                'max_sponsors' => fake()->numberBetween(20, 100),
                'title' => fake()->randomElement([
                    'Annual Women\'s Health Innovation Summit',
                    'International Breast Cancer Research Conference',
                    'Women\'s Healthcare Leadership Forum',
                    'Global Women\'s Wellness Foundation Gala'
                ]),
            ];
        });
    }
}
