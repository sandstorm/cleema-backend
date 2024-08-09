<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Files>
 */
class FilesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->firstName();
        $ext = '.png';
        $hash = hash('sha256', $name);

        return [
            'name' => $name.$ext,
            'alternative_text' => fake()->text(),
            'caption' => fake()->text(),
            'width' => 300,
            'height' => 300,
            'formats' => null,
            'hash' => $hash.$ext,
            'ext' => '.png',
            'mime' => 'image/png',
            'size' => 200.65,
            'url' => 'uploads/'.$hash.$ext,
            'folder_path' => null,
        ];
    }
}
