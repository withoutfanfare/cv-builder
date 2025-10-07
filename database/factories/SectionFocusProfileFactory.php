<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SectionFocusProfile>
 */
class SectionFocusProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate 3-5 section IDs
        $sectionIds = $this->faker->randomElements(range(1, 10), $this->faker->numberBetween(3, 5));

        // section_order is the same IDs but potentially reordered
        $sectionOrder = $this->faker->shuffle($sectionIds);

        return [
            'cv_id' => \App\Models\Cv::factory(),
            'name' => $this->faker->randomElement([
                'Frontend Focus',
                'Backend Focus',
                'Full Stack Focus',
                'Management Focus',
                'Technical Lead Focus',
            ]),
            'included_section_ids' => $sectionIds,
            'section_order' => $sectionOrder,
        ];
    }
}
