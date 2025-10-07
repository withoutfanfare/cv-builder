<?php

use App\Models\Cv;
use App\Models\CvSection;

test('cv has one header info relationship', function () {
    $cv = Cv::factory()->create();

    expect($cv->headerInfo())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class);
});

test('cv has many sections relationship', function () {
    $cv = Cv::factory()->create();

    expect($cv->sections())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('cv sections are ordered by display_order', function () {
    $cv = Cv::factory()->create();

    CvSection::create(['cv_id' => $cv->id, 'section_type' => 'summary', 'display_order' => 2]);
    CvSection::create(['cv_id' => $cv->id, 'section_type' => 'skills', 'display_order' => 1]);
    CvSection::create(['cv_id' => $cv->id, 'section_type' => 'experience', 'display_order' => 3]);

    $sections = $cv->sections;

    expect($sections->pluck('display_order')->toArray())->toBe([1, 2, 3]);
});
