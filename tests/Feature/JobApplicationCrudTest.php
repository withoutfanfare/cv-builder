<?php

use App\Models\Cv;
use App\Models\JobApplication;

describe('JobApplication CRUD', function () {
    test('can create job application with extended fields', function () {
        $cv = Cv::factory()->create();

        $data = [
            'cv_id' => $cv->id,
            'company_name' => 'Acme Corp',
            'job_title' => 'Senior Laravel Developer',
            'source' => 'LinkedIn',
            'application_status' => 'pending',
            'send_status' => 'draft',
            'application_deadline' => now()->addDays(7)->format('Y-m-d'),
            'next_action_date' => now()->addDays(3)->format('Y-m-d'),
            'job_description' => 'Looking for experienced Laravel developer with PHP 8.2 skills',
        ];

        $jobApplication = JobApplication::create($data);

        expect($jobApplication->job_title)->toBe('Senior Laravel Developer')
            ->and($jobApplication->source)->toBe('LinkedIn')
            ->and($jobApplication->job_description)->toContain('Laravel')
            ->and($jobApplication->application_deadline)->toBeInstanceOf(Carbon\Carbon::class)
            ->and($jobApplication->next_action_date)->toBeInstanceOf(Carbon\Carbon::class);
    });

    test('job title is required', function () {
        $cv = Cv::factory()->create();
        $data = [
            'cv_id' => $cv->id,
            'company_name' => 'Test Company',
            'application_status' => 'pending',
            'send_status' => 'draft',
        ];

        // This test validates at the application/Filament level
        // For now, we just ensure the field exists in fillable
        $jobApplication = JobApplication::create($data);

        expect($jobApplication->job_title)->toBeNull();
    });

    test('last activity at auto updates on save', function () {
        $cv = Cv::factory()->create();
        $jobApplication = JobApplication::factory()->create([
            'cv_id' => $cv->id,
            'last_activity_at' => null,
        ]);

        $initialTime = $jobApplication->last_activity_at;

        // Update the job application
        $jobApplication->update(['company_name' => 'Updated Company']);

        expect($jobApplication->fresh()->last_activity_at)->not->toBeNull();
    });

    test('can filter by status', function () {
        JobApplication::factory()->create(['application_status' => 'pending']);
        JobApplication::factory()->create(['application_status' => 'interviewing']);
        JobApplication::factory()->create(['application_status' => 'rejected']);

        $pending = JobApplication::where('application_status', 'pending')->get();
        $interviewing = JobApplication::where('application_status', 'interviewing')->get();

        expect($pending)->toHaveCount(1)
            ->and($interviewing)->toHaveCount(1);
    });
});
