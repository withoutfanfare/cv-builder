<?php

use App\Models\CoverLetter;
use App\Models\JobApplication;
use App\Services\CoverLetterService;

test('can create cover letter with interpolation', function () {
    $application = JobApplication::factory()->create([
        'company_name' => 'Test Company',
        'job_title' => 'Developer',
    ]);

    $service = app(CoverLetterService::class);
    $template = 'Dear {{company_name}}, applying for {{role_title}}';
    $body = $service->interpolate($template, [
        'company_name' => $application->company_name,
        'role_title' => $application->job_title,
    ]);

    $coverLetter = CoverLetter::create([
        'job_application_id' => $application->id,
        'template' => $template,
        'body' => $body,
        'tone' => 'formal',
    ]);

    expect($coverLetter->body)->toBe('Dear Test Company, applying for Developer')
        ->and($coverLetter->tone)->toBe('formal');
});

test('tone must be valid enum', function ($invalidTone) {
    $application = JobApplication::factory()->create();

    expect(fn () => CoverLetter::create([
        'job_application_id' => $application->id,
        'template' => 'Test',
        'body' => 'Test body',
        'tone' => $invalidTone,
    ]))->toThrow(\Exception::class);
})->with(['invalid_tone', 'super_formal', 'relaxed']);

test('all valid tone enum values are accepted', function ($tone) {
    $application = JobApplication::factory()->create();

    $coverLetter = CoverLetter::create([
        'job_application_id' => $application->id,
        'template' => 'Test template',
        'body' => 'Test body',
        'tone' => $tone,
    ]);

    expect($coverLetter->tone)->toBe($tone)
        ->and($coverLetter)->toBeInstanceOf(CoverLetter::class);
})->with(['formal', 'casual', 'enthusiastic', 'technical', 'leadership']);

test('version auto increments per application', function () {
    $application = JobApplication::factory()->create();

    $letter1 = CoverLetter::create([
        'job_application_id' => $application->id,
        'template' => 'Version 1',
        'body' => 'Version 1 body',
        'tone' => 'formal',
    ]);

    $letter2 = CoverLetter::create([
        'job_application_id' => $application->id,
        'template' => 'Version 2',
        'body' => 'Version 2 body',
        'tone' => 'casual',
    ]);

    expect($letter1->version)->toBe(1)
        ->and($letter2->version)->toBe(2);
});

test('cannot update sent cover letter', function () {
    $letter = CoverLetter::factory()->create([
        'is_sent' => true,
        'sent_at' => now(),
    ]);

    expect(fn () => $letter->update(['body' => 'Updated body']))
        ->toThrow(\Exception::class);
});

test('marking as sent sets timestamp', function () {
    $letter = CoverLetter::factory()->create(['is_sent' => false]);

    $letter->update([
        'is_sent' => true,
        'sent_at' => now(),
    ]);

    $letter->refresh();

    expect($letter->is_sent)->toBeTrue()
        ->and($letter->sent_at)->not->toBeNull();
});
