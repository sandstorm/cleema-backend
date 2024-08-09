<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminUsers>
 */
class AdminUsersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->userName,
            'firstname' => fake()->firstName,
            'lastname' => fake()->lastName,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'email' => fake()->unique()->safeEmail,
            'password' => fake()->password,
            'is_active' => true,
            'blocked' => false
        ];
    }
}
