<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SkillEvidence>
 */
class SkillEvidenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skills = ['React', 'Vue', 'Python', 'PHP', 'Laravel', 'Node.js', 'TypeScript', 'JavaScript', 'SQL', 'Docker', 'AWS', 'Git'];

        // For now, default to a polymorphic type - this will be overridden in tests with actual models
        return [
            'cv_id' => \App\Models\Cv::factory(),
            'skill_name' => $this->faker->randomElement($skills),
            'evidenceable_type' => 'App\\Models\\CvSection', // Default to CvSection
            'evidenceable_id' => 1, // Will be set properly in tests
            'notes' => $this->faker->optional(0.7)->sentence(),
        ];
    }
}
