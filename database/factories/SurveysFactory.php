<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Surveys>
 */
class SurveysFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name(),
            'description' => fake()->text(),
            'survey_url' => 'https://cleema.app',
            'evaluation_url' => 'https://cleema.app',
            'target' => 'all',
            'finished' => false,
            'trophy_processed' => false,
        ];
    }
}
