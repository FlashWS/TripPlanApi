<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->country(),
            'date_start' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'date_end' => $this->faker->dateTimeBetween('+1 month', '+2 month'),
            'note' => fake()->text(),
        ];
    }
}
