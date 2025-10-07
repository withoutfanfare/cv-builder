<?php

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\SectionFocusProfile;

test('can create profile with valid sections', function () {
    $cv = Cv::factory()->create();
    $sections = CvSection::factory()->count(5)->create(['cv_id' => $cv->id]);

    $profile = SectionFocusProfile::create([
        'cv_id' => $cv->id,
        'name' => 'Frontend Focus',
        'included_section_ids' => [$sections[0]->id, $sections[2]->id],
        'section_order' => [$sections[2]->id, $sections[0]->id],
    ]);

    expect($profile)->toBeInstanceOf(SectionFocusProfile::class)
        ->and($profile->name)->toBe('Frontend Focus')
        ->and($profile->included_section_ids)->toBe([$sections[0]->id, $sections[2]->id])
        ->and($profile->section_order)->toBe([$sections[2]->id, $sections[0]->id]);
});

test('profile name must be unique per cv', function () {
    $cv = Cv::factory()->create();

    SectionFocusProfile::create([
        'cv_id' => $cv->id,
        'name' => 'Duplicate Name',
        'included_section_ids' => [1],
        'section_order' => [1],
    ]);

    expect(fn () => SectionFocusProfile::create([
        'cv_id' => $cv->id,
        'name' => 'Duplicate Name',
        'included_section_ids' => [2],
        'section_order' => [2],
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

test('applying profile filters and reorders sections', function () {
    $cv = Cv::factory()->create();
    $sections = CvSection::factory()->count(5)->create(['cv_id' => $cv->id]);

    $profile = SectionFocusProfile::create([
        'cv_id' => $cv->id,
        'name' => 'Test Profile',
        'included_section_ids' => [$sections[2]->id, $sections[4]->id],
        'section_order' => [$sections[4]->id, $sections[2]->id],
    ]);

    $filteredSections = $cv->getSectionsWithProfile($profile->id);

    expect($filteredSections)->toHaveCount(2)
        ->and($filteredSections->first()->id)->toBe($sections[4]->id)
        ->and($filteredSections->last()->id)->toBe($sections[2]->id);
});

test('deleting profile does not affect cv', function () {
    $cv = Cv::factory()->create();
    $sectionsCount = CvSection::where('cv_id', $cv->id)->count();

    $profile = SectionFocusProfile::create([
        'cv_id' => $cv->id,
        'name' => 'Test Profile',
        'included_section_ids' => [1],
        'section_order' => [1],
    ]);

    $profile->delete();

    expect(CvSection::where('cv_id', $cv->id)->count())->toBe($sectionsCount)
        ->and(SectionFocusProfile::find($profile->id))->toBeNull();
});
