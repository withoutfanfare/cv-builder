<?php

use App\Models\Cv;
use App\Models\JobApplication;
use App\Models\PDFSnapshot;
use Illuminate\Support\Facades\Storage;

describe('PDF Snapshot Creation', function () {
    beforeEach(function () {
        Storage::fake('local');
    });

    test('pdf snapshot created when send status changes to sent', function () {
        $cv = Cv::factory()->create();
        $jobApplication = JobApplication::factory()->create([
            'cv_id' => $cv->id,
            'send_status' => 'draft',
        ]);

        expect(PDFSnapshot::where('job_application_id', $jobApplication->id)->exists())->toBeFalse();

        // Change send_status to 'sent'
        $jobApplication->update(['send_status' => 'sent']);

        // PDF snapshot should be created
        $snapshot = PDFSnapshot::where('job_application_id', $jobApplication->id)->first();
        expect($snapshot)->not->toBeNull()
            ->and($snapshot->cv_id)->toBe($cv->id)
            ->and($snapshot->job_application_id)->toBe($jobApplication->id);
    });

    test('snapshot file exists in storage', function () {
        $cv = Cv::factory()->create();
        $jobApplication = JobApplication::factory()->create([
            'cv_id' => $cv->id,
            'send_status' => 'draft',
        ]);

        $jobApplication->update(['send_status' => 'sent']);

        $snapshot = PDFSnapshot::where('job_application_id', $jobApplication->id)->first();
        Storage::disk('local')->assertExists($snapshot->file_path);
    });

    test('snapshot hash matches file content', function () {
        $cv = Cv::factory()->create();
        $jobApplication = JobApplication::factory()->create([
            'cv_id' => $cv->id,
            'send_status' => 'draft',
        ]);

        $jobApplication->update(['send_status' => 'sent']);

        $snapshot = PDFSnapshot::where('job_application_id', $jobApplication->id)->first();
        $fileContent = Storage::disk('local')->get($snapshot->file_path);
        $expectedHash = hash('sha256', $fileContent);

        expect($snapshot->hash)->toBe($expectedHash);
    });

    test('snapshot is immutable', function () {
        $cv = Cv::factory()->create();
        $jobApplication = JobApplication::factory()->create([
            'cv_id' => $cv->id,
            'send_status' => 'draft',
        ]);

        $jobApplication->update(['send_status' => 'sent']);

        $snapshot = PDFSnapshot::where('job_application_id', $jobApplication->id)->first();
        $originalHash = $snapshot->hash;
        $originalPath = $snapshot->file_path;

        // Changing send_status again should NOT create a new snapshot
        $jobApplication->update(['send_status' => 'draft']);
        $jobApplication->update(['send_status' => 'sent']);

        $snapshot->refresh();
        expect($snapshot->hash)->toBe($originalHash)
            ->and($snapshot->file_path)->toBe($originalPath)
            ->and(PDFSnapshot::where('job_application_id', $jobApplication->id)->count())->toBe(1);
    });
});
