<?php

use App\Services\KeywordScoringService;

test('prominence weighting applies correct multipliers', function () {
    $jobDescription = "Senior React Developer\n\n".
        "We're seeking a React expert with TypeScript experience.\n\n".
        "You'll work with React, Node.js, and PostgreSQL.";

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    expect($scores['Senior'])->toBe(3.0)  // 3x title
        ->and($scores['React'])->toBe(6.0)  // 3x + 2x + 1x
        ->and($scores['Developer'])->toBe(3.0)  // 3x title
        ->and($scores['TypeScript'])->toBe(2.0)  // 2x intro
        ->and($scores['PostgreSQL'])->toBe(1.0);  // 1x body
});

test('empty description returns empty scores', function () {
    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore('', '');

    expect($scores)->toBe([]);
});

test('keywords appearing in multiple sections get cumulative scores', function () {
    $jobDescription = "PHP Developer\n\nWe need PHP skills.\n\nPHP is essential.";

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    // PHP appears in: title (3x), intro (2x), body (1x) = 6.0
    expect($scores['PHP'])->toBe(6.0);
});

test('stop words are filtered out', function () {
    $jobDescription = "The Senior Developer\n\nWe are looking for a developer.\n\nYou will work with our team.";

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    // Stop words should not appear
    expect($scores)->not->toHaveKey('the')
        ->not->toHaveKey('The')
        ->not->toHaveKey('are')
        ->not->toHaveKey('for')
        ->not->toHaveKey('with')
        // Meaningful words should appear
        ->toHaveKey('Senior')
        ->toHaveKey('Developer');
});

test('short words are filtered out', function () {
    $jobDescription = 'Senior Developer with C++ and Go experience';

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    // Single-letter words filtered (C gets split from ++)
    expect($scores)->not->toHaveKey('C')
        // Two-letter words kept (Go is 2 letters, meets >= 2 requirement)
        ->toHaveKey('Go')
        // Multi-letter words kept
        ->toHaveKey('Senior')
        ->toHaveKey('Developer');
});

test('case insensitive keyword matching', function () {
    $jobDescription = "React Developer\n\nreact experience needed\n\nREACT skills required";

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    // All variations should be counted together under first occurrence case
    expect($scores['React'])->toBe(6.0);  // 3x + 2x + 1x
});

test('handles description with no intro paragraph', function () {
    $jobDescription = 'PHP Developer';

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    expect($scores['PHP'])->toBe(3.0)  // Only title
        ->and($scores['Developer'])->toBe(3.0);
});

test('handles description with no body', function () {
    $jobDescription = "PHP Developer\n\nLooking for PHP expert.";

    $service = app(KeywordScoringService::class);
    $scores = $service->calculateProminenceScore($jobDescription, '');

    expect($scores['PHP'])->toBe(5.0);  // 3x title + 2x intro
});
