<?php

use App\Services\KeywordCoverageService;

describe('Keyword Coverage', function () {
    beforeEach(function () {
        $this->service = new KeywordCoverageService;
    });

    test('keyword coverage calculates correctly', function () {
        $jobDescription = 'We need a PHP Laravel developer with MySQL Redis and Docker experience';
        $cvContent = 'Experienced PHP developer with Laravel MySQL and strong Docker skills';

        $result = $this->service->calculateCoverage($jobDescription, $cvContent);

        expect($result['coverage_percentage'])->toBeGreaterThan(60)
            ->and($result['coverage_percentage'])->toBeLessThanOrEqual(100)
            ->and($result['matched_count'])->toBeGreaterThan(3)
            ->and($result['total_job_keywords'])->toBeGreaterThan(0);
    });

    test('missing keywords limited to 20', function () {
        // Create job description with 30 unique keywords
        $keywords = array_map(fn ($i) => "keyword{$i}", range(1, 30));
        $jobDescription = implode(' ', $keywords);
        $cvContent = 'Some unrelated content without those keywords';

        $result = $this->service->calculateCoverage($jobDescription, $cvContent);

        expect($result['missing_keywords'])->toHaveCount(20)
            ->and($result['coverage_percentage'])->toBe(0.0);
    });

    test('coverage handles no matches', function () {
        $jobDescription = 'Java Spring Boot developer needed';
        $cvContent = 'PHP Laravel developer with MySQL';

        $result = $this->service->calculateCoverage($jobDescription, $cvContent);

        expect($result['coverage_percentage'])->toBeLessThan(30)
            ->and($result['matched_count'])->toBeLessThanOrEqual(1); // Might match "developer"
    });

    test('coverage handles 100 percent match', function () {
        $jobDescription = 'PHP Laravel MySQL';
        $cvContent = 'PHP Laravel MySQL experience';

        $result = $this->service->calculateCoverage($jobDescription, $cvContent);

        expect($result['coverage_percentage'])->toBe(100.0)
            ->and($result['matched_count'])->toBe(3)
            ->and($result['missing_keywords'])->toBeEmpty();
    });
});
