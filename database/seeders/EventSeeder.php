<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventSponsorship;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sponsor users (assuming role_id 3 is for sponsors)
        $sponsors = User::where('role_id', 3)->get();
        
        if ($sponsors->isEmpty()) {
            $this->command->warn('No sponsor users found. Please seed users first.');
            return;
        }
        
        // Create a variety of events
        
        // 1. Upcoming events open for registration
        $upcomingEvents = Event::factory(8)->openForRegistration()->create();
        
        // 2. Past completed events with full sponsorships
        $pastEvents = Event::factory(5)->past()->create();
        
        // 3. High-value events
        $highValueEvents = Event::factory(3)->highValue()->openForRegistration()->create();
        
        // 4. Fully funded events
        $fullyFundedEvents = Event::factory(2)->fullyFunded()->create();
        
        // Add some sponsorships to events
        $allEvents = Event::all();
        
        foreach ($allEvents as $event) {
            // Determine how many sponsors this event should have based on its status
            $sponsorCount = match ($event->status) {
                'completed' => rand(3, min(8, $sponsors->count())), // Completed events have more sponsors
                'ongoing' => rand(2, min(5, $sponsors->count())),   // Ongoing events have some sponsors
                'upcoming' => rand(0, min(3, $sponsors->count())),  // Upcoming events have fewer sponsors
                default => rand(0, min(2, $sponsors->count()))
            };
            
            if ($sponsorCount > 0) {
                $selectedSponsors = $sponsors->random(min($sponsorCount, $sponsors->count()));
                
                foreach ($selectedSponsors as $sponsor) {
                    // Calculate a reasonable sponsorship amount
                    $maxAmount = $event->funding_goal ? $event->funding_goal / 4 : 5000;
                    $minAmount = $event->funding_goal ? $event->funding_goal / 20 : 500;
                    $amount = fake()->randomFloat(2, $minAmount, $maxAmount);
                    
                    // Determine registration status based on event status
                    $registrationStatus = match ($event->status) {
                        'completed' => 'confirmed',
                        'ongoing' => fake()->randomElement(['confirmed', 'confirmed', 'confirmed', 'pending']),
                        'upcoming' => fake()->randomElement(['confirmed', 'confirmed', 'pending']),
                        'cancelled' => fake()->randomElement(['confirmed', 'cancelled']),
                        default => 'pending'
                    };
                    
                    $registeredAt = fake()->dateTimeBetween('-2 months', 'now');
                    
                    $confirmedAt = $registrationStatus === 'confirmed' 
                        ? fake()->dateTimeBetween($registeredAt, 'now')
                        : null;
                    
                    EventSponsorship::create([
                        'event_id' => $event->id,
                        'sponsor_id' => $sponsor->id,
                        'amount' => $amount,
                        'registration_status' => $registrationStatus,
                        'message' => fake()->optional(0.6)->sentence(),
                        'registered_at' => $registeredAt,
                        'confirmed_at' => $confirmedAt,
                    ]);
                }
            }
            
            // Update some fully funded events to actually be fully funded
            if ($event->funding_goal) {
                $currentFunding = $event->confirmedSponsorships()->sum('amount');
                $remaining = $event->funding_goal - $currentFunding;
                
                // If this was marked as a "fully funded" event, add the remaining amount
                if ($remaining > 0 && $event->status === 'upcoming' && fake()->boolean(30)) {
                    $finalSponsor = $sponsors->random();
                    EventSponsorship::create([
                        'event_id' => $event->id,
                        'sponsor_id' => $finalSponsor->id,
                        'amount' => $remaining,
                        'registration_status' => 'confirmed',
                        'message' => 'Completing the funding goal for this important event.',
                        'registered_at' => fake()->dateTimeBetween('-1 month', 'now'),
                        'confirmed_at' => fake()->dateTimeBetween('-1 month', 'now'),
                    ]);
                }
            }
        }
        
        $this->command->info('✅ Created ' . Event::count() . ' events with sponsorships');
        $this->command->info('📊 Event breakdown:');
        $this->command->info('   - Upcoming: ' . Event::where('status', 'upcoming')->count());
        $this->command->info('   - Ongoing: ' . Event::where('status', 'ongoing')->count());
        $this->command->info('   - Completed: ' . Event::where('status', 'completed')->count());
        $this->command->info('   - Cancelled: ' . Event::where('status', 'cancelled')->count());
        $this->command->info('💰 Total sponsorships: ' . EventSponsorship::count());
    }
}
