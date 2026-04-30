<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Genre>
 */
class GenreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genres = ['Action', 'Comedy', 'Drama', 'Horror', 'Sci-Fi', 'Thriller'];
        return [
            //
            'name' => fake()->unique()->randomElement($genres),
        ];
    }
}
