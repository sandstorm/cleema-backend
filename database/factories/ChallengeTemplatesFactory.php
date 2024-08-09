<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChallengeTemplates>
 */
class ChallengeTemplatesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->text(40),
            'description' => fake()->text(),
            'teaser_text' => fake()->text(),
            'interval' => fake()->randomElement(['daily', 'weekly']),
            'kind' => fake()->randomElement(['user', 'group']),
            'goal_type' => 'steps',
        ];
    }
}
