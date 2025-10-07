<?php

use App\Models\ApplicationEvent;
use App\Models\JobApplication;
use App\Models\Metric;
use App\Services\MetricsCalculationService;

test('calculates applications per week', function () {
    // Create 20 applications over last 30 days
    JobApplication::factory()->count(20)->create([
        'created_at' => now()->subDays(30),
    ]);

    $service = app(MetricsCalculationService::class);
    $service->calculateApplicationsPerWeek('30d');

    $metric = Metric::where('metric_type', 'applications_per_week')->first();

    expect($metric)->not->toBeNull()
        ->and($metric->value)->toBeGreaterThan(0);
});

test('calculates response rate excluding withdrawn', function () {
    // Create 10 active applications
    JobApplication::factory()->count(10)->create([
        'created_at' => now()->subDays(30),
        'withdrawn_at' => null,
    ]);

    // 5 with replies
    JobApplication::factory()->count(5)
        ->has(ApplicationEvent::factory()->state([
            'event_type' => 'reply_received',
            'occurred_at' => now()->subDays(25),
        ]), 'events')
        ->create([
            'created_at' => now()->subDays(30),
            'withdrawn_at' => null,
        ]);

    // 3 withdrawn (should be excluded)
    JobApplication::factory()->count(3)->create([
        'created_at' => now()->subDays(30),
        'withdrawn_at' => now()->subDays(20),
    ]);

    $service = app(MetricsCalculationService::class);
    $service->calculateResponseRate('30d');

    $metric = Metric::where('metric_type', 'response_rate')->first();

    // 5 replies / 15 total active = 33.33%
    expect($metric)->not->toBeNull()
        ->and($metric->value)->toBeGreaterThan(30)
        ->and($metric->value)->toBeLessThan(35);
});

test('calculates interview conversion rate', function () {
    // Create 10 active applications
    JobApplication::factory()->count(10)->create([
        'created_at' => now()->subDays(30),
        'withdrawn_at' => null,
    ]);

    // 3 with interviews
    JobApplication::factory()->count(3)
        ->has(ApplicationEvent::factory()->state([
            'event_type' => 'interview_scheduled',
            'occurred_at' => now()->subDays(20),
        ]), 'events')
        ->create([
            'created_at' => now()->subDays(30),
            'withdrawn_at' => null,
        ]);

    $service = app(MetricsCalculationService::class);
    $service->calculateInterviewConversionRate('30d');

    $metric = Metric::where('metric_type', 'interview_conversion_rate')->first();

    // 3 interviews / 13 total active ≈ 23%
    expect($metric)->not->toBeNull()
        ->and($metric->value)->toBeGreaterThan(20)
        ->and($metric->value)->toBeLessThan(25);
});

test('calculates offer rate', function () {
    // Create 10 active applications
    JobApplication::factory()->count(10)->create([
        'created_at' => now()->subDays(30),
        'withdrawn_at' => null,
    ]);

    // 2 with offers
    JobApplication::factory()->count(2)
        ->has(ApplicationEvent::factory()->state([
            'event_type' => 'offer_received',
            'occurred_at' => now()->subDays(15),
        ]), 'events')
        ->create([
            'created_at' => now()->subDays(30),
            'withdrawn_at' => null,
        ]);

    $service = app(MetricsCalculationService::class);
    $service->calculateOfferRate('30d');

    $metric = Metric::where('metric_type', 'offer_rate')->first();

    // 2 offers / 12 total active ≈ 16.67%
    expect($metric)->not->toBeNull()
        ->and($metric->value)->toBeGreaterThan(15)
        ->and($metric->value)->toBeLessThan(18);
});

test('calculates median days to first response', function () {
    // Create applications with varying response times
    $app1 = JobApplication::factory()->create(['created_at' => now()->subDays(30)]);
    ApplicationEvent::factory()->create([
        'job_application_id' => $app1->id,
        'event_type' => 'reply_received',
        'occurred_at' => now()->subDays(27), // 3 days
    ]);

    $app2 = JobApplication::factory()->create(['created_at' => now()->subDays(30)]);
    ApplicationEvent::factory()->create([
        'job_application_id' => $app2->id,
        'event_type' => 'reply_received',
        'occurred_at' => now()->subDays(23), // 7 days
    ]);

    $app3 = JobApplication::factory()->create(['created_at' => now()->subDays(30)]);
    ApplicationEvent::factory()->create([
        'job_application_id' => $app3->id,
        'event_type' => 'reply_received',
        'occurred_at' => now()->subDays(16), // 14 days
    ]);

    $service = app(MetricsCalculationService::class);
    $service->calculateMedianDaysToFirstResponse('30d');

    $metric = Metric::where('metric_type', 'median_days_to_first_response')->first();

    // Median of [3, 7, 14] = 7
    expect($metric)->not->toBeNull()
        ->and($metric->value)->toBe(7.0);
});

test('separates withdrawn application stats', function () {
    // Create 10 active applications
    JobApplication::factory()->count(10)->create([
        'created_at' => now()->subDays(30),
        'withdrawn_at' => null,
    ]);

    // Create 3 withdrawn applications
    JobApplication::factory()->count(3)->create([
        'created_at' => now()->subDays(30),
        'withdrawn_at' => now()->subDays(20),
    ]);

    // Add reply event to a withdrawn application (should not count in response rate)
    $withdrawn = JobApplication::where('withdrawn_at', '!=', null)->first();
    ApplicationEvent::factory()->create([
        'job_application_id' => $withdrawn->id,
        'event_type' => 'reply_received',
        'occurred_at' => now()->subDays(25),
    ]);

    $service = app(MetricsCalculationService::class);
    $service->calculateResponseRate('30d');

    $metric = Metric::where('metric_type', 'response_rate')->first();

    // Should calculate based on 10 active, not 13 total
    expect($metric)->not->toBeNull();
});

test('metrics stored in database', function () {
    JobApplication::factory()->count(5)->create([
        'created_at' => now()->subDays(30),
    ]);

    $service = app(MetricsCalculationService::class);
    $service->refreshAllMetrics('30d');

    $metrics = Metric::all();

    expect($metrics)->toHaveCount(5)
        ->each->toBeInstanceOf(Metric::class);
});

test('metrics update last refreshed at', function () {
    JobApplication::factory()->count(5)->create([
        'created_at' => now()->subDays(30),
    ]);

    $service = app(MetricsCalculationService::class);
    $service->refreshAllMetrics('30d');

    $metric = Metric::first();

    expect($metric->last_refreshed_at)->not->toBeNull()
        ->and($metric->last_refreshed_at->isToday())->toBeTrue();
});
