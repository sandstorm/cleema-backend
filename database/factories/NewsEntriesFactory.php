<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsEntries>
 */
class NewsEntriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'views' => fake()->numberBetween(0, 100),
            'description' => fake()->paragraph(),
            'teaser' => fake()->sentence(),
            'type' => fake()->randomElement(['tip', 'news']),
            'published_at' => Carbon::now()->subDay()->toString(),
            'date' => Carbon::now()->subDay()->toDateString(),
            "locale" => 'de-DE',
            // 'tags' => fake()->numberBetween(1, 11)
        ];
    }
}
