<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Challenges>
 */
class ChallengesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->text(),
            'end_date' => Carbon::now()->addWeek(),
            'start_date' => Carbon::now()->subWeek(),
            'kind' => 'partner',
            'interval' => 'daily',
            'is_public' => 0,
            'teaser_text' => fake()->text(),
            'title' => fake()->text(),
            'goal_type' => 'steps',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'published_at' => Carbon::now()->subWeek(),
            'trophy_processed' => false,
            'views' => 0,
            'locale' => 'de-DE'
        ];
    }
}
