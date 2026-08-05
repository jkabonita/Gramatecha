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
        $fileTypes = ['pptx', 'mp4', 'mov'];
        $fileType = fake()->randomElement($fileTypes);
        
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'filename' => fake()->word() . '.' . $fileType,
            'file_size' => fake()->numberBetween(100000, 50000000),
            'file_type' => $fileType,
            'cloudinary_public_id' => 'presentations/' . fake()->uuid(),
            'cloudinary_url' => fake()->url(),
            'uploaded_by' => User::factory(),
        ];
    }
}
