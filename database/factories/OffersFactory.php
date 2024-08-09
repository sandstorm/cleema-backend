<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offers>
 */
class OffersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->title(),
            'url' => 'https://cleema.app',
            'summary' => fake()->text(),
            'description' => fake()->text(),
            'discount' => fake()->numberBetween(0, 100),
            'valid_from' => Carbon::now()->subWeek(),
            'valid_until' => Carbon::now()->addWeek(),
            'is_regional' => fake()->randomElement([true, false]),
            'store_type' => fake()->randomElement(['shop', 'online']),
            'locale' => 'de-DE',
            'generic_voucher' => fake()->word(),
            'redeem_interval' => fake()->randomElement(['1', '7', '30', '365']),
        ];
    }
}
