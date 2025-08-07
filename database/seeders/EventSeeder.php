<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        EventType::factory()->count(5)->create();

        $organizers = User::factory()->count(5)->create();

        Event::factory()->count(10)->create();

        $volunteers = User::factory()->count(20)->create();

        $events = Event::all();

        foreach ($events as $event) {
            $randomVolunteers = $volunteers->random(rand(5, 10));
            foreach ($randomVolunteers as $volunteer) {
                DB::table('event_volunteer')->insert([
                    'event_id' => $event->id,
                    'user_id' => $volunteer->id,
                    'user_type' => 'volunteer',
                    'status' => fake()->randomElement(['approved', 'attended', 'absent']),
                    'registered_at' => now()->subDays(rand(0, 30)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
