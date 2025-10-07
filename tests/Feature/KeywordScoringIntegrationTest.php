<?php

use App\Services\KeywordScoringService;

test('scenario 5: advanced keyword scoring with prominence weighting', function () {
    $jobDescription = "Senior React Developer\n\n".
        "We're seeking a React expert with TypeScript experience to join our frontend team.\n\n".
        "You'll work with React, Node.js, and PostgreSQL daily. ".
        'Our stack includes TypeScript for type safety.';

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    // Verify prominence weighting
    // "React" appears in: title (3x), intro (2x), body (1x) = 6.0
    expect($scores['React'])->toBe(6.0)
        // "TypeScript" appears in: intro (2x), body (1x) = 3.0
        ->and($scores['TypeScript'])->toBe(3.0)
        // "PostgreSQL" appears in: body (1x) = 1.0
        ->and($scores['PostgreSQL'])->toBe(1.0)
        // "Senior" appears in: title (3x) = 3.0
        ->and($scores['Senior'])->toBe(3.0)
        // "Developer" appears in: title (3x) = 3.0
        ->and($scores['Developer'])->toBe(3.0);
});

test('keyword scoring handles different section structures', function () {
    $jobDescription = "Frontend Engineer\n\n".
        "Looking for a skilled engineer.\n\n".
        'Experience with Vue required.';

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    // "Frontend" in title only
    expect($scores['Frontend'])->toBe(3.0)
        // "Engineer" in title (3x) and intro (2x) = 5.0
        ->and($scores['Engineer'])->toBe(5.0)
        // "Vue" in body only
        ->and($scores['Vue'])->toBe(1.0);
});

test('keyword scoring filters out stop words', function () {
    $jobDescription = "The Senior Developer\n\nWe are looking for a developer.\n\nYou will work with our team.";

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    // Stop words should not appear
    expect($scores)->not->toHaveKey('the')
        ->not->toHaveKey('are')
        ->not->toHaveKey('for')
        ->not->toHaveKey('with');

    // Meaningful words should appear
    expect($scores)->toHaveKey('Senior')
        ->toHaveKey('Developer');
});

test('keyword scoring handles empty sections gracefully', function () {
    $jobDescription = "\n\n\n";

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    expect($scores)->toBe([]);
});

test('keyword scoring counts multiple occurrences in same section', function () {
    $jobDescription = 'React React React Developer';

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    // "React" appears 3 times in title, each gets 3x weight = 9.0
    expect($scores['React'])->toBe(9.0);
});
