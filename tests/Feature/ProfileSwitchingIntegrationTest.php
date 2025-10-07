<?php

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\SectionFocusProfile;

test('scenario 6: non-destructive profile switching preserves original data', function () {
    $cv = Cv::factory()->create();

    // Create sections representing different skill areas
    $frontendSection = CvSection::factory()->create([
        'cv_id' => $cv->id,
        'title' => 'Frontend Experience',
        'display_order' => 1,
    ]);

    $backendSection = CvSection::factory()->create([
        'cv_id' => $cv->id,
        'title' => 'Backend Experience',
        'display_order' => 2,
    ]);

    $fullStackSection = CvSection::factory()->create([
        'cv_id' => $cv->id,
        'title' => 'Full Stack Projects',
        'display_order' => 3,
    ]);

    // Create "Frontend Focus" profile
    $frontendProfile = SectionFocusProfile::create([
        'cv_id' => $cv->id,
        'name' => 'Frontend Focus',
        'included_section_ids' => [$frontendSection->id, $fullStackSection->id],
        'section_order' => [$fullStackSection->id, $frontendSection->id],
    ]);

    // Create "Backend Focus" profile
    $backendProfile = SectionFocusProfile::create([
        'cv_id' => $cv->id,
        'name' => 'Backend Focus',
        'included_section_ids' => [$backendSection->id, $fullStackSection->id],
        'section_order' => [$backendSection->id, $fullStackSection->id],
    ]);

    // Apply frontend profile
    $frontendSections = $cv->getSectionsWithProfile($frontendProfile->id);
    expect($frontendSections)->toHaveCount(2)
        ->and($frontendSections->first()->id)->toBe($fullStackSection->id)
        ->and($frontendSections->last()->id)->toBe($frontendSection->id);

    // Switch to backend profile instantly
    $backendSections = $cv->getSectionsWithProfile($backendProfile->id);
    expect($backendSections)->toHaveCount(2)
        ->and($backendSections->first()->id)->toBe($backendSection->id)
        ->and($backendSections->last()->id)->toBe($fullStackSection->id);

    // Confirm no data loss - all original sections still exist
    $cv->refresh();
    expect($cv->sections)->toHaveCount(3)
        ->and($cv->sectionFocusProfiles)->toHaveCount(2);

    // Original display order unchanged
    $originalSections = $cv->sections()->orderBy('display_order')->get();
    expect($originalSections->first()->id)->toBe($frontendSection->id)
        ->and($originalSections->last()->id)->toBe($fullStackSection->id);
});

test('profile switching works with empty profile', function () {
    $cv = Cv::factory()->create();
    $sections = CvSection::factory()->count(3)->create(['cv_id' => $cv->id]);

    // Create empty profile (no sections)
    $emptyProfile = SectionFocusProfile::create([
        'cv_id' => $cv->id,
        'name' => 'Minimal Profile',
        'included_section_ids' => [],
        'section_order' => [],
    ]);

    $filteredSections = $cv->getSectionsWithProfile($emptyProfile->id);

    expect($filteredSections)->toHaveCount(0);

    // Original sections unchanged
    $cv->refresh();
    expect($cv->sections)->toHaveCount(3);
});

test('profile can be deleted without affecting CV sections', function () {
    $cv = Cv::factory()->create();
    $sections = CvSection::factory()->count(2)->create(['cv_id' => $cv->id]);

    $profile = SectionFocusProfile::create([
        'cv_id' => $cv->id,
        'name' => 'Test Profile',
        'included_section_ids' => [$sections[0]->id],
        'section_order' => [$sections[0]->id],
    ]);

    // Delete profile
    $profile->delete();

    // CV sections remain intact
    $cv->refresh();
    expect($cv->sections)->toHaveCount(2)
        ->and($cv->sectionFocusProfiles)->toHaveCount(0);
});
