<?php

namespace Database\Factories;

use App\Models\Volume;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Volume>
 */
class VolumeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'no' => fake()->numberBetween(0, 20),
            'chapters_total' => fake()->numberBetween(0,20),
        ];
    }
}
