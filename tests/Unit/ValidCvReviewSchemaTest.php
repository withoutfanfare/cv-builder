<?php

use App\Rules\ValidCvReviewSchema;

// T029: Unit test - ValidCvReviewSchema validates correct structure
test('valid cv review schema passes validation', function () {
    $rule = new ValidCvReviewSchema;
    $validData = [
        'schema_version' => '1.0',
        'match_score' => 75,
        'analysis_metadata' => [
            'generated_at' => now()->toIso8601String(),
            'model_used' => 'gpt-4-turbo-preview',
            'tokens_used' => 4500,
            'prompt_version' => '1.0',
        ],
        'skill_gaps' => [
            ['skill' => 'Kubernetes', 'priority' => 'high', 'reason' => 'Required', 'suggestion' => 'Add experience'],
        ],
        'section_recommendations' => [
            ['section' => 'Experience', 'impact' => 'medium', 'recommendation' => 'Move to top'],
        ],
        'bullet_improvements' => [
            ['current' => 'Worked on projects', 'suggested' => 'Led 5 projects', 'priority' => 'emphasize'],
        ],
        'language_suggestions' => [],
        'skill_evidence' => [],
        'action_checklist' => ['Add Kubernetes experience'],
    ];

    $failed = false;
    $rule->validate('ai_review_data', $validData, function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeFalse();
});

test('invalid match score fails validation', function () {
    $rule = new ValidCvReviewSchema;
    $invalidData = [
        'schema_version' => '1.0',
        'match_score' => 150, // Invalid - over 100
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
    ];

    $failed = false;
    $rule->validate('ai_review_data', $invalidData, function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeTrue();
});

test('missing required field fails validation', function () {
    $rule = new ValidCvReviewSchema;
    $invalidData = [
        'schema_version' => '1.0',
        'match_score' => 75,
        // Missing analysis_metadata
        'skill_gaps' => [],
        'section_recommendations' => [],
        'bullet_improvements' => [],
        'language_suggestions' => [],
        'skill_evidence' => [],
        'action_checklist' => [],
    ];

    $failed = false;
    $rule->validate('ai_review_data', $invalidData, function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeTrue();
});

test('invalid priority enum fails validation', function () {
    $rule = new ValidCvReviewSchema;
    $invalidData = [
        'schema_version' => '1.0',
        'match_score' => 75,
        'analysis_metadata' => [
            'generated_at' => now()->toIso8601String(),
            'model_used' => 'gpt-4-turbo-preview',
            'tokens_used' => 4500,
            'prompt_version' => '1.0',
        ],
        'skill_gaps' => [
            ['skill' => 'Kubernetes', 'priority' => 'urgent', 'reason' => 'Required'], // Invalid priority
        ],
        'section_recommendations' => [],
        'bullet_improvements' => [],
        'language_suggestions' => [],
        'skill_evidence' => [],
        'action_checklist' => [],
    ];

    $failed = false;
    $rule->validate('ai_review_data', $invalidData, function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeTrue();
});

test('non-array value fails validation', function () {
    $rule = new ValidCvReviewSchema;
    $invalidData = 'not an array';

    $failed = false;
    $rule->validate('ai_review_data', $invalidData, function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeTrue();
});
