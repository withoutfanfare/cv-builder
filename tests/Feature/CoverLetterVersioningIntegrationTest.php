<?php

use App\Models\CoverLetter;
use App\Models\JobApplication;

test('scenario 3: cover letter A/B testing with version control', function () {
    $application = JobApplication::factory()->create();

    // Create version 1 with formal tone
    $letter1 = CoverLetter::create([
        'job_application_id' => $application->id,
        'template' => 'Template text',
        'body' => 'Dear Sir/Madam, I am writing to express my interest...',
        'tone' => 'formal',
    ]);

    // Create version 2 with enthusiastic tone
    $letter2 = CoverLetter::create([
        'job_application_id' => $application->id,
        'template' => 'Template text',
        'body' => 'Hi team! I am super excited to apply...',
        'tone' => 'enthusiastic',
    ]);

    // Verify auto-incremented versions
    expect($letter1->version)->toBe(1)
        ->and($letter2->version)->toBe(2);

    // Mark letter 1 as sent
    $letter1->update([
        'is_sent' => true,
        'sent_at' => now(),
    ]);

    // Verify immutability of sent letter
    expect(fn () => $letter1->update(['body' => 'Updated body']))
        ->toThrow(\Exception::class, 'Cannot update a sent cover letter');

    // Letter 2 can still be edited
    $letter2->update(['body' => 'Updated enthusiastic body']);
    expect($letter2->body)->toBe('Updated enthusiastic body');
});

test('multiple versions can exist in different states', function () {
    $application = JobApplication::factory()->create();

    $draft = CoverLetter::create([
        'job_application_id' => $application->id,
        'body' => 'Draft body',
        'tone' => 'formal',
        'is_sent' => false,
    ]);

    $sent = CoverLetter::create([
        'job_application_id' => $application->id,
        'body' => 'Sent body',
        'tone' => 'technical',
        'is_sent' => true,
        'sent_at' => now(),
    ]);

    expect($draft->is_sent)->toBeFalse()
        ->and($sent->is_sent)->toBeTrue()
        ->and($application->coverLetters)->toHaveCount(2)
        ->and($application->getLatestCoverLetter()->id)->toBe($sent->id)
        ->and($application->getSentCoverLetter()->id)->toBe($sent->id);
});
