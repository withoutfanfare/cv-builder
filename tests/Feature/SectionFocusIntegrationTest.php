<?php

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\SectionFocusProfile;

test('scenario 1: section focus profiles work end-to-end', function () {
    // Create CV with sections
    $cv = Cv::factory()->create();
    $sections = CvSection::factory()->count(5)->create([
        'cv_id' => $cv->id,
    ]);

    // Original display order
    $originalSectionIds = $cv->sections()->pluck('id')->toArray();

    // Create profile with custom section order
    $profile = SectionFocusProfile::create([
        'cv_id' => $cv->id,
        'name' => 'Frontend Focus',
        'included_section_ids' => [$sections[1]->id, $sections[3]->id, $sections[4]->id],
        'section_order' => [$sections[4]->id, $sections[1]->id, $sections[3]->id],
    ]);

    // Apply profile (non-destructive)
    $filteredSections = $cv->getSectionsWithProfile($profile->id);

    // Verify filtered and reordered
    expect($filteredSections)->toHaveCount(3)
        ->and($filteredSections->first()->id)->toBe($sections[4]->id)
        ->and($filteredSections->last()->id)->toBe($sections[3]->id);

    // Verify non-destructive (original CV unchanged)
    $cv->refresh();
    expect($cv->sections()->pluck('id')->toArray())->toBe($originalSectionIds);
});

test('profile application does not modify source data', function () {
    $cv = Cv::factory()->create();
    $sections = CvSection::factory()->count(3)->create(['cv_id' => $cv->id]);

    $profile = SectionFocusProfile::create([
        'cv_id' => $cv->id,
        'name' => 'Test Profile',
        'included_section_ids' => [$sections[0]->id],
        'section_order' => [$sections[0]->id],
    ]);

    // Apply profile multiple times
    $cv->getSectionsWithProfile($profile->id);
    $cv->getSectionsWithProfile($profile->id);

    // Source data should remain unchanged
    $cv->refresh();
    expect($cv->sections)->toHaveCount(3);
});
