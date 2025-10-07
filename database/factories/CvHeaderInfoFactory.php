<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CvHeaderInfo>
 */
class CvHeaderInfoFactory extends Factory
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
            'full_name' => fake()->name(),
            'job_title' => fake()->jobTitle(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'location' => fake()->city().', '.fake()->country(),
            'linkedin_url' => fake()->optional()->url(),
            'github_url' => fake()->optional()->url(),
            'website_url' => fake()->optional()->url(),
        ];
    }
}
