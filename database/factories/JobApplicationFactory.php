<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobApplication>
 */
class JobApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $applicationStatus = fake()->randomElement([
            'pending',
            'reviewed',
            'interviewing',
            'offered',
            'rejected',
            'accepted',
            'withdrawn',
        ]);

        $interviewDates = [];
        if (in_array($applicationStatus, ['interviewing', 'offered', 'accepted', 'rejected'])) {
            $count = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $interviewDates[] = [
                    'date' => fake()->dateTimeBetween('-1 month', '+1 week')->format('Y-m-d H:i:s'),
                    'type' => fake()->randomElement([
                        'Phone Screen',
                        'Technical Interview',
                        'Team Interview',
                        'Final Interview',
                        'HR Interview',
                    ]),
                ];
            }
        }

        return [
            'cv_id' => \App\Models\Cv::factory(),
            'company_name' => fake()->company(),
            'company_website' => fake()->optional(0.7)->url(),
            'company_notes' => fake()->optional(0.5)->paragraph(),
            'point_of_contact_name' => fake()->optional(0.6)->name(),
            'point_of_contact_email' => fake()->optional(0.6)->companyEmail(),
            'send_status' => fake()->randomElement(['draft', 'sent']),
            'application_status' => $applicationStatus,
            'interview_dates' => empty($interviewDates) ? null : $interviewDates,
            'interview_notes' => fake()->optional(0.4)->paragraph(),
            'notes' => fake()->optional(0.6)->paragraph(),
        ];
    }
}
