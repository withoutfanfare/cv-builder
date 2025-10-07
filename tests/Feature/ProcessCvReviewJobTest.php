<?php

use App\Jobs\ProcessCvReview;
use App\Models\Cv;
use App\Models\JobApplication;
use App\Services\CvReviewService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

// T011: Job test - ProcessCvReview job updates job application
test('process cv review job updates job application', function () {
    Queue::fake();

    Http::fake([
        'api.openai.com/*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'schema_version' => '1.0',
                            'match_score' => 85,
                            'analysis_metadata' => [
                                'generated_at' => now()->toIso8601String(),
                                'model_used' => 'gpt-4-turbo-preview',
                                'tokens_used' => 5000,
                                'prompt_version' => '1.0',
                            ],
                            'skill_gaps' => [],
                            'section_recommendations' => [],
                            'bullet_improvements' => [],
                            'language_suggestions' => [],
                            'skill_evidence' => [],
                            'action_checklist' => [],
                        ]),
                    ],
                ],
            ],
            'usage' => ['total_tokens' => 5000],
        ]),
    ]);

    // Create CV with skills
    $cv = Cv::factory()->withSkills()->create();

    $application = JobApplication::factory()
        ->for($cv)
        ->create([
            'job_description' => 'Software engineer role with PHP and Laravel requiring 3+ years experience.',
            'ai_review_requested_at' => now(),
        ]);

    // Execute job directly (not queued)
    $job = new ProcessCvReview($application);
    $job->handle(app(CvReviewService::class));

    $application->refresh();

    expect($application->ai_review_completed_at)->not->toBeNull();
    expect($application->ai_review_data)->not->toBeNull();
    expect($application->ai_review_cost_cents)->toBeGreaterThan(0);
    expect($application->ai_review_data['match_score'])->toBe(85);
});
