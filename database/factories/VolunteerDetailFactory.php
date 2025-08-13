<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VolunteerDetail>
 */
class VolunteerDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'skills' => $this->faker->text(100),
            'interests' => $this->faker->text(100),
            'availability_schedule' => $this->faker->sentence(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
            'date_joined_from_form' => $this->faker->dateTimeBetween('-2 years'),
            'total_hours_volunteered' => $this->faker->randomFloat(2, 5, 200),
            'volunteering_type_preference' => $this->faker->randomElement(['remote', 'on_site', 'both']),
            'address' => $this->faker->address(),
        ];
    }
}
