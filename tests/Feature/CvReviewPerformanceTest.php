<?php

use App\Models\Cv;
use App\Models\JobApplication;
use App\Services\CvReviewService;
use Illuminate\Support\Facades\Http;

// T031: Performance test - review completes in < 10 seconds
test('cv review completes in under 10 seconds for standard cv', function () {
    Http::fake([
        'api.openai.com/*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'schema_version' => '1.0',
                            'match_score' => 75,
                            'analysis_metadata' => [
                                'generated_at' => now()->toIso8601String(),
                                'model_used' => 'gpt-4-turbo-preview',
                                'tokens_used' => 4500,
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
            'usage' => ['total_tokens' => 4500],
        ]),
    ]);

    // Create CV with typical content (< 1000 words)
    $cv = Cv::factory()->withSkills()->create();

    $application = JobApplication::factory()
        ->for($cv)
        ->create([
            'job_description' => 'Senior Full Stack Developer role requiring PHP, Laravel, MySQL, JavaScript, and React. 5+ years experience required. Strong communication and leadership skills essential. Experience with microservices architecture, Docker, and CI/CD pipelines preferred.',
        ]);

    $service = app(CvReviewService::class);

    $startTime = microtime(true);
    $service->analyzeForJob($cv, $application);
    $endTime = microtime(true);

    $executionTime = $endTime - $startTime;

    expect($executionTime)->toBeLessThan(10.0);
})->group('performance');
