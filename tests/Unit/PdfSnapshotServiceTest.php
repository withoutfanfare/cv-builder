<?php

use App\Models\Cv;
use App\Models\JobApplication;
use App\Models\PDFSnapshot;
use App\Services\PdfSnapshotService;
use Illuminate\Support\Facades\Storage;

describe('PdfSnapshotService', function () {
    beforeEach(function () {
        Storage::fake('local');
        $this->service = app(PdfSnapshotService::class);
    });

    test('create generates pdf and calculates hash', function () {
        $cv = Cv::factory()->create(['title' => 'Test CV']);
        $jobApplication = JobApplication::factory()->create([
            'cv_id' => $cv->id,
            'company_name' => 'Test Company',
        ]);

        $snapshot = $this->service->create($jobApplication);

        expect($snapshot)->toBeInstanceOf(PDFSnapshot::class)
            ->and($snapshot->hash)->toHaveLength(64) // SHA-256 hash
            ->and($snapshot->file_path)->toContain('pdf-snapshots')
            ->and($snapshot->job_application_id)->toBe($jobApplication->id);
    });

    test('create stores file at correct path', function () {
        $cv = Cv::factory()->create();
        $jobApplication = JobApplication::factory()->create(['cv_id' => $cv->id]);

        $snapshot = $this->service->create($jobApplication);

        Storage::disk('local')->assertExists($snapshot->file_path);
        expect($snapshot->file_path)->toMatch('/pdf-snapshots\/\d+_[a-f0-9]{64}\.pdf/');
    });

    test('hash is sha256 of pdf content', function () {
        $cv = Cv::factory()->create();
        $jobApplication = JobApplication::factory()->create(['cv_id' => $cv->id]);

        $snapshot = $this->service->create($jobApplication);

        $fileContent = Storage::disk('local')->get($snapshot->file_path);
        $expectedHash = hash('sha256', $fileContent);

        expect($snapshot->hash)->toBe($expectedHash);
    });

    test('create throws if cv missing', function () {
        $jobApplication = JobApplication::factory()->make(['cv_id' => null]);
        $jobApplication->id = 999; // Set ID for path generation

        expect(fn () => $this->service->create($jobApplication))
            ->toThrow(Exception::class);
    });
});
