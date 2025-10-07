<?php

use App\Models\Cv;
use App\Models\JobApplication;
use App\Models\PDFSnapshot;

describe('CV Soft Delete', function () {
    test('delete soft deletes cv', function () {
        $cv = Cv::factory()->create();

        $cv->delete();

        expect($cv->deleted_at)->not->toBeNull()
            ->and(Cv::find($cv->id))->toBeNull(); // Not in default queries
    });

    test('deleted cv excluded from default queries', function () {
        $activeCv = Cv::factory()->create(['title' => 'Active CV']);
        $deletedCv = Cv::factory()->create(['title' => 'Deleted CV']);
        $deletedCv->delete();

        $allCvs = Cv::all();

        expect($allCvs)->toHaveCount(1)
            ->and($allCvs->first()->title)->toBe('Active CV');
    });

    test('withTrashed includes deleted cvs', function () {
        $activeCv = Cv::factory()->create(['title' => 'Active CV']);
        $deletedCv = Cv::factory()->create(['title' => 'Deleted CV']);
        $deletedCv->delete();

        $allCvs = Cv::withTrashed()->get();

        expect($allCvs)->toHaveCount(2);
    });

    test('job applications accessible after cv deleted', function () {
        $cv = Cv::factory()->create();
        $jobApplication = JobApplication::factory()->create([
            'cv_id' => $cv->id,
            'company_name' => 'Test Company',
        ]);

        $cv->delete();

        // Job application should still be accessible
        $foundJobApp = JobApplication::find($jobApplication->id);
        expect($foundJobApp)->not->toBeNull()
            ->and($foundJobApp->cv_id)->toBe($cv->id)
            ->and($foundJobApp->company_name)->toBe('Test Company');
    });

    test('pdf snapshots preserved after cv deleted', function () {
        $cv = Cv::factory()->create();
        $jobApplication = JobApplication::factory()->create(['cv_id' => $cv->id]);
        $snapshot = PDFSnapshot::factory()->create([
            'job_application_id' => $jobApplication->id,
            'cv_id' => $cv->id,
            'file_path' => 'pdf-snapshots/test.pdf',
            'hash' => str_repeat('a', 64),
        ]);

        $cv->delete();

        // PDF snapshot should still exist
        $foundSnapshot = PDFSnapshot::find($snapshot->id);
        expect($foundSnapshot)->not->toBeNull()
            ->and($foundSnapshot->cv_id)->toBe($cv->id)
            ->and($foundSnapshot->file_path)->toBe('pdf-snapshots/test.pdf');
    });
});
