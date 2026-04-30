<?php

namespace Database\Factories;

use App\Models\Novel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Novel>
 */
class NovelFactory extends Factory
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
            'title' => fake()->sentences(1,true),
            'cover' => 'https://picsum.photos/1080/1920?random=' . fake()->numberBetween(1, 1000),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['completed', 'ongoing']),
            'stars' => fake()->numberBetween(0, 1000),
            'readed' => fake()->numberBetween(0, 1000),
            'published_year' => fake()->numberBetween(2000, 2026),
            'downloaded' => fake()->numberBetween(0, 1000),
            'volumes_total' => fake()->numberBetween(1, 20),
        ];
    }
}
