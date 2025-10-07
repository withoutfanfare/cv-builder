<?php

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\SectionFocusProfile;
use App\Services\CoverLetterService;
use App\Services\KeywordScoringService;

test('profile application completes in under 100ms', function () {
    // Create CV with 10 sections
    $cv = Cv::factory()->create();
    CvSection::factory()->count(10)->create(['cv_id' => $cv->id]);

    // Create 5 profiles
    $profiles = [];
    for ($i = 0; $i < 5; $i++) {
        $sectionIds = $cv->sections()->pluck('id')->random(5)->toArray();
        $profiles[] = SectionFocusProfile::create([
            'cv_id' => $cv->id,
            'name' => "Profile $i",
            'included_section_ids' => $sectionIds,
            'section_order' => shuffle($sectionIds) ? $sectionIds : $sectionIds,
        ]);
    }

    // Benchmark getSectionsWithProfile()
    $startTime = microtime(true);

    foreach ($profiles as $profile) {
        $cv->getSectionsWithProfile($profile->id);
    }

    $endTime = microtime(true);
    $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds

    // Average time per profile should be under 100ms
    $avgDuration = $duration / 5;

    expect($avgDuration)->toBeLessThan(100.0);
})->group('performance');

test('keyword scoring completes in under 1 second for 5000 words', function () {
    // Generate large job description (~5000 words)
    $words = [
        'React', 'TypeScript', 'JavaScript', 'Node.js', 'PostgreSQL',
        'Docker', 'Kubernetes', 'AWS', 'Senior', 'Developer',
        'Experience', 'Team', 'Leadership', 'Agile', 'Scrum',
    ];

    $paragraph = implode(' ', array_map(fn () => $words[array_rand($words)], range(1, 500)));
    $jobDescription = "Senior Full Stack Developer\n\n".
        "We are seeking an experienced developer.\n\n".
        str_repeat($paragraph.' ', 10); // ~5000 words total

    $service = app(KeywordScoringService::class);

    // Benchmark calculateProminenceScore()
    $startTime = microtime(true);

    $scores = $service->calculateProminenceScore($jobDescription, '');

    $endTime = microtime(true);
    $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds

    expect($duration)->toBeLessThan(1000.0)
        ->and($scores)->not->toBeEmpty();
})->group('performance');

test('cover letter interpolation completes in under 200ms', function () {
    $service = app(CoverLetterService::class);

    // Template with multiple variables
    $template = str_repeat(
        'Dear {{company_name}}, I am {{applicant_name}} applying for {{role_title}}. '.
            '{{value_prop}} and {{recent_win}}. I bring {{years_experience}} years of experience. '.
            'My skills include {{skill_1}}, {{skill_2}}, and {{skill_3}}. '.
            "I am excited about {{company_value}}. Best regards, {{applicant_name}}.\n\n",
        10
    );

    $variables = [
        'company_name' => 'Acme Corporation',
        'applicant_name' => 'John Doe',
        'role_title' => 'Senior Laravel Developer',
        'value_prop' => 'I have extensive experience building scalable web applications',
        'recent_win' => 'I recently led a project that increased conversion rates by 45%',
        'years_experience' => '8',
        'skill_1' => 'Laravel',
        'skill_2' => 'React',
        'skill_3' => 'PostgreSQL',
        'company_value' => 'innovation and teamwork',
    ];

    // Benchmark interpolate()
    $startTime = microtime(true);

    // Run interpolation 100 times to get average
    for ($i = 0; $i < 100; $i++) {
        $result = $service->interpolate($template, $variables);
    }

    $endTime = microtime(true);
    $duration = (($endTime - $startTime) * 1000) / 100; // Average time in milliseconds

    expect($duration)->toBeLessThan(200.0)
        ->and($result)->not->toContain('{{');
})->group('performance');
