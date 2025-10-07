<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PdfTemplate>
 */
class PdfTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => \Str::slug($name),
            'description' => fake()->sentence(),
            'view_path' => 'cv.templates.'.fake()->slug(),
            'preview_image_path' => 'template-previews/'.fake()->slug().'.png',
            'is_default' => false,
        ];
    }
}
