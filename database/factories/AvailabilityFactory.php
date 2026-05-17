<?php

namespace Database\Factories;

use App\Models\Availability;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvailabilityFactory extends Factory
{
    protected $model = Availability::class;

    public function definition(): array
    {
        return [
            // Generate a random date between the start of the current month and the end of the next month
            'date' => $this->faker->dateTimeBetween(
                now()->startOfMonth(),
                now()->addMonth()->endOfMonth()
            )->format('Y-m-d'),

            // Generate a random hour between 08:00 and 17:00
            'hour' => sprintf('%02d:00:00', $this->faker->numberBetween(8, 17)),

            // Randomly set as available or unavailable
            'is_available' => $this->faker->boolean(70), // 70% chance of being true
        ];
    }
}
