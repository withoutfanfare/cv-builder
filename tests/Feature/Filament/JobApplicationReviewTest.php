<?php

use App\Filament\Resources\JobApplications\Pages\EditJobApplication;
use App\Jobs\ProcessCvReview;
use App\Models\Cv;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

// T012: Filament test - Review action visible for draft applications
test('review action visible for draft applications', function () {
    $cv = Cv::factory()->create();
    $application = JobApplication::factory()
        ->for($cv)
        ->create([
            'send_status' => 'draft',
            'job_description' => 'Software Engineer role requiring PHP, Laravel, and 3+ years of experience.',
        ]);

    Livewire::test(EditJobApplication::class, [
        'record' => $application->id,
    ])
        ->assertActionExists('reviewCv');
});

// T013: Filament test - Review action triggers ProcessCvReview job
test('review action triggers job', function () {
    Queue::fake();

    $cv = Cv::factory()->create();
    $application = JobApplication::factory()
        ->for($cv)
        ->create([
            'send_status' => 'draft',
            'job_description' => 'Software Engineer role requiring PHP, Laravel, and 3+ years of experience.',
        ]);

    Livewire::test(EditJobApplication::class, [
        'record' => $application->id,
    ])
        ->callAction('reviewCv');

    Queue::assertPushed(ProcessCvReview::class, function ($job) use ($application) {
        return $job->jobApplication->id === $application->id;
    });
});

// T014: Filament test - Review results display after completion
test('review results display after completion', function () {
    $cv = Cv::factory()->create();
    $application = JobApplication::factory()
        ->for($cv)
        ->create([
            'send_status' => 'draft',
            'job_description' => 'Software Engineer role',
            'ai_review_completed_at' => now(),
            'ai_review_data' => [
                'schema_version' => '1.0',
                'match_score' => 75,
                'analysis_metadata' => [
                    'generated_at' => now()->toIso8601String(),
                    'model_used' => 'gpt-4-turbo-preview',
                    'tokens_used' => 4500,
                    'prompt_version' => '1.0',
                ],
                'skill_gaps' => [
                    [
                        'skill' => 'Kubernetes',
                        'priority' => 'high',
                        'reason' => 'Required in job',
                        'suggestion' => 'Add experience',
                    ],
                ],
                'section_recommendations' => [],
                'bullet_improvements' => [],
                'language_suggestions' => [],
                'skill_evidence' => [],
                'action_checklist' => ['Add Kubernetes'],
            ],
        ]);

    Livewire::test(EditJobApplication::class, [
        'record' => $application->id,
    ])
        ->assertSee('75') // Match score
        ->assertSee('Skill Gaps') // Tab name
        ->assertSee('Kubernetes'); // Skill gap content
});

// T015: Filament test - Stale review indicator shows when CV updated
test('stale review indicator shows when cv updated', function () {
    $cv = Cv::factory()->create();

    $reviewTime = now()->subHour();
    $cvUpdateTime = now();

    $application = JobApplication::factory()
        ->for($cv)
        ->create([
            'send_status' => 'draft',
            'job_description' => 'Software Engineer role',
            'ai_review_completed_at' => $reviewTime,
            'ai_review_data' => [
                'schema_version' => '1.0',
                'match_score' => 70,
                'analysis_metadata' => [
                    'generated_at' => $reviewTime->toIso8601String(),
                    'model_used' => 'gpt-4-turbo-preview',
                    'tokens_used' => 4000,
                    'prompt_version' => '1.0',
                ],
                'skill_gaps' => [],
                'section_recommendations' => [],
                'bullet_improvements' => [],
                'language_suggestions' => [],
                'skill_evidence' => [],
                'action_checklist' => [],
            ],
        ]);

    // Update CV after review
    $cv->update(['updated_at' => $cvUpdateTime]);

    Livewire::test(EditJobApplication::class, [
        'record' => $application->id,
    ])
        ->assertSee('Review Out of Date') // Stale warning
        ->assertActionExists('regenerateReview'); // Regenerate button
});
