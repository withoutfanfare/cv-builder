<?php

use App\Models\ApplicationEvent;
use App\Models\JobApplication;

test('can create application event', function () {
    $application = JobApplication::factory()->create();

    $event = ApplicationEvent::create([
        'job_application_id' => $application->id,
        'event_type' => 'interview_scheduled',
        'occurred_at' => now(),
        'notes' => 'First round technical interview',
        'metadata' => ['format' => 'video'],
    ]);

    expect($event)->toBeInstanceOf(ApplicationEvent::class)
        ->event_type->toBe('interview_scheduled')
        ->notes->toBe('First round technical interview');
});

test('event type is required', function () {
    $application = JobApplication::factory()->create();

    $this->expectException(\Illuminate\Database\QueryException::class);

    ApplicationEvent::create([
        'job_application_id' => $application->id,
        'occurred_at' => now(),
    ]);
});

test('event type must be valid enum', function () {
    $application = JobApplication::factory()->create();

    $this->expectException(\Illuminate\Database\QueryException::class);

    ApplicationEvent::create([
        'job_application_id' => $application->id,
        'event_type' => 'invalid_type',
        'occurred_at' => now(),
    ]);
});

test('occurred at is required', function () {
    $application = JobApplication::factory()->create();

    $this->expectException(\Illuminate\Database\QueryException::class);

    ApplicationEvent::create([
        'job_application_id' => $application->id,
        'event_type' => 'submitted',
    ]);
});

test('creating event updates parent last activity at', function () {
    $application = JobApplication::factory()->create([
        'last_activity_at' => null,
    ]);

    $eventTime = now()->subDays(2);

    ApplicationEvent::create([
        'job_application_id' => $application->id,
        'event_type' => 'reply_received',
        'occurred_at' => $eventTime,
    ]);

    $application->refresh();

    expect($application->last_activity_at)->not->toBeNull()
        ->and($application->last_activity_at->format('Y-m-d H:i'))
        ->toBe($eventTime->format('Y-m-d H:i'));
});

test('can list application events chronologically', function () {
    $application = JobApplication::factory()->create();

    ApplicationEvent::factory()->create([
        'job_application_id' => $application->id,
        'event_type' => 'submitted',
        'occurred_at' => now()->subDays(10),
    ]);

    ApplicationEvent::factory()->create([
        'job_application_id' => $application->id,
        'event_type' => 'reply_received',
        'occurred_at' => now()->subDays(5),
    ]);

    ApplicationEvent::factory()->create([
        'job_application_id' => $application->id,
        'event_type' => 'interview_scheduled',
        'occurred_at' => now()->subDays(2),
    ]);

    $events = $application->events;

    expect($events)->toHaveCount(3)
        ->and($events->first()->event_type)->toBe('interview_scheduled')
        ->and($events->last()->event_type)->toBe('submitted');
});

test('can delete application event', function () {
    $event = ApplicationEvent::factory()->create();

    $event->delete();

    expect(ApplicationEvent::find($event->id))->toBeNull();
});

test('metadata can store flexible json', function () {
    $application = JobApplication::factory()->create();

    $metadata = [
        'format' => 'video',
        'interviewers' => 'Jane Smith, John Doe',
        'prep_topics' => 'React hooks, state management',
    ];

    $event = ApplicationEvent::create([
        'job_application_id' => $application->id,
        'event_type' => 'interview_scheduled',
        'occurred_at' => now(),
        'metadata' => $metadata,
    ]);

    expect($event->fresh()->metadata)
        ->toBeArray()
        ->toMatchArray($metadata);
});
