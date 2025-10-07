<?php

use App\Services\KeywordCoverageService;

describe('KeywordCoverageService', function () {
    beforeEach(function () {
        $this->service = new KeywordCoverageService;
    });

    test('tokenize removes stopwords and short tokens', function () {
        $text = 'The quick brown fox jumps over the lazy dog in a very fast way';
        $tokens = $this->service->tokenize($text);

        expect($tokens)->not->toContain('the')
            ->and($tokens)->not->toContain('a')
            ->and($tokens)->not->toContain('in')
            ->and($tokens)->toContain('quick')
            ->and($tokens)->toContain('brown')
            ->and($tokens)->toContain('fox');
    });

    test('calculate coverage returns correct percentage', function () {
        $jobDescription = 'We need PHP Laravel developer with MySQL and Redis experience';
        $cvContent = 'Experienced PHP developer with Laravel framework and MySQL database skills';

        $result = $this->service->calculateCoverage($jobDescription, $cvContent);

        expect($result)->toHaveKeys(['coverage_percentage', 'missing_keywords', 'matched_count', 'total_job_keywords'])
            ->and($result['coverage_percentage'])->toBeGreaterThan(50)
            ->and($result['matched_count'])->toBeGreaterThan(0)
            ->and($result['total_job_keywords'])->toBeGreaterThan(0);
    });

    test('calculate coverage limits missing keywords to 20', function () {
        // Generate 30 unique keywords
        $keywords = [];
        for ($i = 1; $i <= 30; $i++) {
            $keywords[] = "keyword{$i}";
        }
        $jobDescription = implode(' ', $keywords);
        $cvContent = 'Some unrelated content here';

        $result = $this->service->calculateCoverage($jobDescription, $cvContent);

        expect($result['missing_keywords'])->toHaveCount(20);
    });

    test('calculate coverage handles empty job description', function () {
        $jobDescription = '';
        $cvContent = 'Some CV content';

        $result = $this->service->calculateCoverage($jobDescription, $cvContent);

        expect($result['coverage_percentage'])->toBe(0.0)
            ->and($result['missing_keywords'])->toBeEmpty()
            ->and($result['matched_count'])->toBe(0)
            ->and($result['total_job_keywords'])->toBe(0);
    });
});
