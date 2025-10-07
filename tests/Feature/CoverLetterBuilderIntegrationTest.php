<?php

use App\Models\CoverLetter;
use App\Models\JobApplication;
use App\Services\CoverLetterService;

test('scenario 2: cover letter builder interpolates variables from job application', function () {
    $application = JobApplication::factory()->create([
        'company_name' => 'Acme Corp',
        'job_title' => 'Senior Developer',
    ]);

    $template = "Dear Hiring Manager at {{company_name}},\n\n".
        'I am excited to apply for the {{role_title}} position. '.
        "{{value_prop}}.\n\n".
        'Best regards';

    $variables = [
        'company_name' => $application->company_name,
        'role_title' => $application->job_title,
        'value_prop' => 'I bring 5 years of experience in Laravel development',
    ];

    $service = app(CoverLetterService::class);
    $interpolatedBody = $service->interpolate($template, $variables);

    // Create cover letter with interpolated body
    $coverLetter = CoverLetter::create([
        'job_application_id' => $application->id,
        'template' => $template,
        'body' => $interpolatedBody,
        'tone' => 'formal',
    ]);

    // Verify interpolation
    expect($coverLetter->body)->toContain('Acme Corp')
        ->toContain('Senior Developer')
        ->toContain('I bring 5 years of experience')
        ->not->toContain('{{company_name}}')
        ->not->toContain('{{role_title}}');

    // Verify saved to database
    $coverLetter->refresh();
    expect($coverLetter->body)->toContain('Acme Corp');
});

test('cover letter builder preserves template for future edits', function () {
    $application = JobApplication::factory()->create();

    $template = 'Hello {{company_name}}';

    $service = app(CoverLetterService::class);
    $body = $service->interpolate($template, ['company_name' => 'Company A']);

    $coverLetter = CoverLetter::create([
        'job_application_id' => $application->id,
        'template' => $template,
        'body' => $body,
        'tone' => 'casual',
    ]);

    // Can use original template for new version
    $newBody = $service->interpolate($template, ['company_name' => 'Company B']);

    expect($newBody)->toBe('Hello Company B')
        ->and($coverLetter->template)->toBe($template);
});
