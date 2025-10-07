<?php

use App\Exceptions\IncompleteCvException;
use App\Exceptions\MissingJobDescriptionException;
use App\Models\Cv;
use App\Models\JobApplication;
use App\Services\CvReviewService;
use Illuminate\Support\Facades\Http;

// T006: Contract test - analyzeForJob() returns valid structure
test('analyze for job returns valid structure', function () {
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
                            'skill_gaps' => [
                                [
                                    'skill' => 'Kubernetes',
                                    'priority' => 'high',
                                    'reason' => 'Required in job description',
                                    'suggestion' => 'Add container orchestration experience',
                                ],
                            ],
                            'section_recommendations' => [],
                            'bullet_improvements' => [],
                            'language_suggestions' => [],
                            'skill_evidence' => [],
                            'action_checklist' => ['Add Kubernetes experience'],
                        ]),
                    ],
                ],
            ],
            'usage' => ['total_tokens' => 4500],
        ]),
    ]);

    // Create CV with skills
    $cv = Cv::factory()->withSkills()->create();

    $application = JobApplication::factory()
        ->for($cv)
        ->create(['job_description' => 'Backend engineer role requiring PHP, Laravel, and Kubernetes experience. Must have 3+ years experience.']);

    $service = app(CvReviewService::class);
    $result = $service->analyzeForJob($cv, $application);

    expect($result)
        ->toHaveKey('match_score')
        ->toHaveKey('skill_gaps')
        ->toHaveKey('action_checklist')
        ->toHaveKey('analysis_metadata');

    expect($result['match_score'])->toBeBetween(0, 100);
});

// T007: Contract test - throws MissingJobDescriptionException
test('analyze for job throws exception when job description missing', function () {
    $cv = Cv::factory()->create();

    $application = JobApplication::factory()
        ->for($cv)
        ->create(['job_description' => null]);

    $service = app(CvReviewService::class);
    $service->analyzeForJob($cv, $application);
})->throws(MissingJobDescriptionException::class);

// T008: Contract test - throws IncompleteCvException
test('analyze for job throws exception when cv incomplete', function () {
    // Create a CV with no sections/experiences/skills
    $cv = Cv::factory()->create();
    // Ensure it has no skillCategories or experiences by not creating any related records

    $application = JobApplication::factory()
        ->for($cv)
        ->create(['job_description' => 'Backend engineer role requiring PHP and Laravel experience with 3+ years.']);

    $service = app(CvReviewService::class);
    $service->analyzeForJob($cv, $application);
})->throws(IncompleteCvException::class);

// T009: Contract test - extractJobRequirements() parses skills
test('extract job requirements parses skills', function () {
    Http::fake([
        'api.openai.com/*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'skills' => ['Python', 'Docker', 'PostgreSQL'],
                            'competencies' => ['leadership', 'communication'],
                            'keywords' => ['microservices', 'CI/CD'],
                            'experience_level' => 'senior',
                            'role_focus' => ['backend development', 'infrastructure'],
                        ]),
                    ],
                ],
            ],
        ]),
    ]);

    $jobDescription = 'Senior Backend Engineer needed with Python, Docker, PostgreSQL. Leadership and communication skills essential. Experience with microservices and CI/CD required.';

    $service = app(CvReviewService::class);
    $result = $service->extractJobRequirements($jobDescription);

    expect($result)
        ->toHaveKey('skills')
        ->toHaveKey('competencies')
        ->toHaveKey('keywords');

    expect($result['skills'])->toBeArray();
    expect($result['competencies'])->toBeArray();
    expect($result['keywords'])->toBeArray();
});

// T010: Contract test - calculateMatchScore() returns 0-100
test('calculate match score returns 0 to 100', function () {
    $cvData = [
        'skills' => ['PHP', 'Laravel', 'MySQL'],
        'experiences' => [
            ['title' => 'Backend Developer', 'highlights' => ['Built APIs']],
        ],
        'education' => [],
        'highlights' => [],
    ];

    $jobRequirements = [
        'skills' => ['PHP', 'Laravel', 'PostgreSQL'],
        'competencies' => ['problem-solving'],
        'keywords' => ['API', 'database'],
        'experience_level' => 'mid',
        'role_focus' => ['backend development'],
    ];

    $service = app(CvReviewService::class);
    $score = $service->calculateMatchScore($cvData, $jobRequirements);

    expect($score)->toBeInt();
    expect($score)->toBeGreaterThanOrEqual(0);
    expect($score)->toBeLessThanOrEqual(100);
});
