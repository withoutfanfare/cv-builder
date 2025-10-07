<?php

use App\Models\Cv;
use App\Models\CvHeaderInfo;
use App\Models\CvSection;
use App\Models\CVVersion;

describe('CV Cloning', function () {
    test('clone creates full deep copy', function () {
        $originalCv = Cv::factory()->create(['title' => 'Original CV']);
        CvHeaderInfo::factory()->create(['cv_id' => $originalCv->id]);
        CvSection::factory()->count(3)->create(['cv_id' => $originalCv->id]);

        $clonedCv = $originalCv->cloneCv();

        expect($clonedCv)->toBeInstanceOf(Cv::class)
            ->and($clonedCv->id)->not->toBe($originalCv->id)
            ->and($clonedCv->title)->toContain('Copy')
            ->and($clonedCv->sections)->toHaveCount(3);
    });

    test('clone creates cv version snapshot', function () {
        $originalCv = Cv::factory()->create(['title' => 'Original CV']);
        CvSection::factory()->count(2)->create(['cv_id' => $originalCv->id]);

        $clonedCv = $originalCv->cloneCv('Testing clone feature');

        $version = CVVersion::where('cv_id', $originalCv->id)
            ->where('reason', 'Testing clone feature')
            ->first();

        expect($version)->not->toBeNull()
            ->and($version->snapshot_json)->toBeArray()
            ->and($version->created_at)->toBeInstanceOf(Carbon\Carbon::class);
    });

    test('cloned cv has independent sections', function () {
        $originalCv = Cv::factory()->create();
        $originalSection = CvSection::factory()->create([
            'cv_id' => $originalCv->id,
            'section_type' => 'experience',
        ]);

        $clonedCv = $originalCv->cloneCv();
        $clonedSection = $clonedCv->sections()->first();

        // Modify cloned section
        $clonedSection->update(['section_type' => 'projects']);

        // Original should be unchanged
        expect($originalSection->fresh()->section_type)->toBe('experience')
            ->and($clonedSection->section_type)->toBe('projects');
    });

    test('version snapshot json is valid', function () {
        $originalCv = Cv::factory()->create(['title' => 'Test CV']);
        CvSection::factory()->create(['cv_id' => $originalCv->id]);

        $clonedCv = $originalCv->cloneCv();

        $version = CVVersion::where('cv_id', $originalCv->id)->first();
        $snapshot = $version->snapshot_json;

        expect($snapshot)->toBeArray()
            ->and($snapshot)->toHaveKey('id')
            ->and($snapshot)->toHaveKey('title');
    });
});
