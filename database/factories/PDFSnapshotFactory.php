<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PDFSnapshot>
 */
class PDFSnapshotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_application_id' => \App\Models\JobApplication::factory(),
            'cv_id' => \App\Models\Cv::factory(),
            'cv_version_id' => null,
            'file_path' => 'pdf-snapshots/'.fake()->uuid().'.pdf',
            'hash' => fake()->sha256(),
            'created_at' => now(),
        ];
    }
}
