<?php

namespace Database\Factories;

use App\Models\Point;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripPoint>
 */
class TripPointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'point_uuid' => Point::factory(),
            'day' => $this->faker->randomDigitNot(0),
            'time' => $this->faker->time(),
            'note' => fake()->text(),
        ];
    }
}
