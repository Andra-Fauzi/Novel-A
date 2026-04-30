<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
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
            'author_name' => fake()->name(),
            'description' => fake()->paragraph(),
            'biodata' => fake()->paragraph(2),
            'status' => fake()->randomElement(['unverified', 'verified']),
        ];
    }
}
