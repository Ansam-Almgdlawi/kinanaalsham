<?php

namespace Database\Factories;

use App\Models\EventType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $start = $this->faker->dateTimeBetween('-1 month', '+1 week');
        $end = (clone $start)->modify('+3 hours');

        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'event_type_id' => EventType::inRandomOrder()->first()?->id ?? EventType::factory(),
            'start_datetime' => $start,
            'end_datetime' => $end,
            'location_text' => $this->faker->address(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'status' => $this->faker->randomElement(['planned', 'ongoing', 'completed']),
            'organizer_user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'supervisor_user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'target_audience' => $this->faker->word(),
            'max_participants' => $this->faker->numberBetween(10, 100),
            'is_public' => $this->faker->boolean(),
        ];
    }
}
