<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Projects>
 */
class ProjectsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'summary' => fake()->text(),
            'description' => fake()->text(),
            'goal_type' => 'information',
            'phase' => 'running',
            'start_date' => Carbon::now()->subWeek(),
            'published_at' => Carbon::now()->subWeek(),
            'locale' => 'de-DE',
        ];
    }
}
