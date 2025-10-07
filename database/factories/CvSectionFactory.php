<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CvSection>
 */
class CvSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cv_id' => \App\Models\Cv::factory(),
            'section_type' => fake()->randomElement(['experience', 'education', 'skills', 'projects', 'summary']),
            'title' => fake()->words(2, true),
            'display_order' => fake()->numberBetween(1, 10),
        ];
    }
}
