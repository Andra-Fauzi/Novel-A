<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Author;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Comment;
use App\Models\Content;
use App\Models\Genre;
use App\Models\Novel;
use App\Models\Recommend;
use App\Models\Volume;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'andra',
            'email' => 'andra@mail.com',
            'password' => 'andra',
            'role' => 'author'
        ]);

        Author::factory()->create([
            'user_id' => 1,
            'author_name' => 'andra',
        ]);

        // $categories = collect(['Action', 'Romance', 'Fantasy'])
        //     ->map(fn ($name) => Category::firstOrCreate(['name' => $name]));

        // $genres = collect(['Magic', 'School', 'Adventure'])
        //     ->map(fn ($name) => Genre::firstOrCreate(['name' => $name]));

        // User::factory(500)->has(
        //     Author::factory()
        //         ->has(
        //             Novel::factory()->has(
        //                 Volume::factory(10)->has(
        //                     Chapter::factory(10)->has(
        //                         Content::factory()
        //                     )
        //                 )
        //             )
        //             ->hasAttached($categories->random(2))
        //             ->hasAttached($genres->random(2))
        //         )
        // )->create();

        // $recommends = collect([1, 2, 3, 4, 5])->map(fn ($novel_id) => Recommend::firstOrCreate(['novel_id' => $novel_id]));

    }
}
