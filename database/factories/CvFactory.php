<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cv>
 */
class CvFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
        ];
    }

    /**
     * Create CV with skills section for testing
     */
    public function withSkills(): static
    {
        return $this->afterCreating(function ($cv) {
            $section = \App\Models\CvSection::factory()->create([
                'cv_id' => $cv->id,
                'section_type' => 'skills',
                'display_order' => 1,
            ]);

            \App\Models\CvSkillCategory::create([
                'cv_section_id' => $section->id,
                'category_name' => 'Technical Skills',
                'display_order' => 1,
                'skills' => [
                    ['name' => 'PHP'],
                    ['name' => 'Laravel'],
                    ['name' => 'MySQL'],
                ],
            ]);
        });
    }
}
