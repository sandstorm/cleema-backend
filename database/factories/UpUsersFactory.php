<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UpUsers>
 */
class UpUsersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->userName(),
            'is_anonymous' => false,
            'uuid' => fake()->uuid(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'email' => fake()->email(),
            'password' => fake()->password(),
            'referral_code' => fake()->uuid(),
            'referral_count' => 0,
            'confirmed' => true,
            'accepts_surveys' => true,
            'is_supporter' => false,
            'provider' => 'local',
        ];
    }
}
