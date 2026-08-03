<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Presentation>
 */
class PresentationFactory extends Factory
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
            'description' => fake()->paragraph(),
            'filename' => fake()->word() . '.pptx',
            'file_size' => fake()->numberBetween(100000, 50000000),
            'cloudinary_public_id' => 'presentations/' . fake()->uuid(),
            'cloudinary_url' => fake()->url(),
            'uploaded_by' => User::factory(),
        ];
    }
}
