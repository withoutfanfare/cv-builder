<?php

use App\Models\Cv;
use App\Models\JobApplication;

// T030: Unit test - isReviewStale() detects CV modifications
test('is review stale returns false when no review completed', function () {
    $application = JobApplication::factory()
        ->for(Cv::factory())
        ->create([
            'ai_review_completed_at' => null,
        ]);

    expect($application->isReviewStale())->toBeFalse();
});

test('is review stale returns false when cv not modified after review', function () {
    $cv = Cv::factory()->create(['updated_at' => now()->subHours(2)]);

    $application = JobApplication::factory()
        ->for($cv)
        ->create([
            'ai_review_completed_at' => now()->subHour(),
        ]);

    expect($application->isReviewStale())->toBeFalse();
});

test('is review stale returns true when cv modified after review', function () {
    $cv = Cv::factory()->create(['updated_at' => now()]);

    $application = JobApplication::factory()
        ->for($cv)
        ->create([
            'ai_review_completed_at' => now()->subHour(),
        ]);

    expect($application->isReviewStale())->toBeTrue();
});

// T030: Unit test - getReviewData() returns structured data
test('get review data returns null when no review', function () {
    $application = JobApplication::factory()
        ->for(Cv::factory())
        ->create([
            'ai_review_data' => null,
        ]);

    expect($application->getReviewData())->toBeNull();
});

test('get review data returns array when review exists', function () {
    $reviewData = [
        'schema_version' => '1.0',
        'match_score' => 85,
        'skill_gaps' => [],
        'action_checklist' => ['Improve skills section'],
    ];

    $application = JobApplication::factory()
        ->for(Cv::factory())
        ->create([
            'ai_review_data' => $reviewData,
        ]);

    expect($application->getReviewData())->toBe($reviewData);
});
