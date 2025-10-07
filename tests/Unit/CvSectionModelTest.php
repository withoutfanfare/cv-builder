<?php

use App\Models\Cv;
use App\Models\CvSection;

test('cv section belongs to cv relationship', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'summary',
        'display_order' => 1,
    ]);

    expect($section->cv())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($section->cv->id)->toBe($cv->id);
});

test('cv section has one summary relationship', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'summary',
        'display_order' => 1,
    ]);

    expect($section->summary())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class);
});

test('cv section has many skill categories relationship', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'skills',
        'display_order' => 1,
    ]);

    expect($section->skillCategories())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('cv section has many experiences relationship', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'experience',
        'display_order' => 1,
    ]);

    expect($section->experiences())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('cv section has many projects relationship', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'projects',
        'display_order' => 1,
    ]);

    expect($section->projects())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('cv section has many education relationship', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'education',
        'display_order' => 1,
    ]);

    expect($section->education())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('cv section has one reference relationship', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'references',
        'display_order' => 1,
    ]);

    expect($section->reference())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class);
});
