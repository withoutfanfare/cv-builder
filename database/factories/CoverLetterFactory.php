<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoverLetter>
 */
class CoverLetterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $template = "Dear Hiring Manager at {{company_name}},\n\n".
            "I am excited to apply for the {{role_title}} position. {{value_prop}}.\n\n".
            "Recently, {{recent_win}}. I believe this experience makes me an ideal candidate.\n\n".
            'Best regards';

        return [
            'job_application_id' => \App\Models\JobApplication::factory(),
            'template' => $template,
            'body' => str_replace(
                ['{{company_name}}', '{{role_title}}', '{{value_prop}}', '{{recent_win}}'],
                [
                    $this->faker->company(),
                    $this->faker->jobTitle(),
                    'I bring extensive experience in '.$this->faker->word(),
                    'I achieved '.$this->faker->sentence(),
                ],
                $template
            ),
            'tone' => $this->faker->randomElement(['formal', 'casual', 'enthusiastic', 'technical', 'leadership']),
            'version' => 1, // Will be auto-incremented by model
            'is_sent' => false,
            'sent_at' => null,
        ];
    }
}
