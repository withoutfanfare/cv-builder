<?php

namespace Database\Factories;

use App\Models\JobApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationEvent>
 */
class ApplicationEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_application_id' => JobApplication::factory(),
            'event_type' => fake()->randomElement([
                'submitted',
                'reply_received',
                'interview_scheduled',
                'interview_completed',
                'offer_received',
                'rejected',
                'withdrawn',
            ]),
            'occurred_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'notes' => fake()->optional()->sentence(10),
            'metadata' => fake()->optional()->randomElement([
                ['format' => 'video', 'interviewers' => fake()->name()],
                ['went_well' => fake()->sentence(), 'needs_improvement' => fake()->sentence()],
                null,
            ]),
        ];
    }
}
