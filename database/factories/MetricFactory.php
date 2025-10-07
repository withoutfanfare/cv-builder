<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Metric>
 */
class MetricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'metric_type' => fake()->randomElement([
                'applications_per_week',
                'response_rate',
                'interview_conversion_rate',
                'offer_rate',
                'median_days_to_first_response',
            ]),
            'value' => fake()->randomFloat(2, 0, 100),
            'time_period_start' => now()->subDays(30),
            'time_period_end' => now(),
            'last_refreshed_at' => now(),
        ];
    }
}
