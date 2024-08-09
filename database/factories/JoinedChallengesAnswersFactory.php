<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JoinedChallengesAnswers>
 */
class JoinedChallengesAnswersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'answer' => 'succeeded',
            'day_index' => fake()->numberBetween(1,10),
        ];
    }
}
