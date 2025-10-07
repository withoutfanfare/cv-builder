<?php

use App\Models\Cv;
use App\Models\CvExperience;
use App\Models\CvHeaderInfo;
use App\Models\CvSection;
use App\Models\CvSkillCategory;
use App\Models\CvSummary;

test('cv header info belongs to cv', function () {
    $cv = Cv::factory()->create();
    $headerInfo = CvHeaderInfo::create([
        'cv_id' => $cv->id,
        'full_name' => 'John Doe',
        'job_title' => 'Developer',
        'email' => 'john@example.com',
    ]);

    expect($headerInfo->cv())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($headerInfo->cv->id)->toBe($cv->id);
});

test('cv summary belongs to cv section', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'summary',
        'display_order' => 1,
    ]);
    $summary = CvSummary::create([
        'cv_section_id' => $section->id,
        'content' => 'Professional summary',
    ]);

    expect($summary->cvSection())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($summary->cvSection->id)->toBe($section->id);
});

test('cv skill category belongs to cv section and is ordered', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'skills',
        'display_order' => 1,
    ]);

    CvSkillCategory::create([
        'cv_section_id' => $section->id,
        'category_name' => 'Backend',
        'skills' => ['PHP', 'Laravel'],
        'display_order' => 2,
    ]);
    CvSkillCategory::create([
        'cv_section_id' => $section->id,
        'category_name' => 'Frontend',
        'skills' => ['JavaScript', 'Vue'],
        'display_order' => 1,
    ]);

    $categories = $section->skillCategories;

    expect($categories->first()->cvSection())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($categories->pluck('display_order')->toArray())->toBe([1, 2]);
});

test('cv experience belongs to cv section and is ordered', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'experience',
        'display_order' => 1,
    ]);

    CvExperience::create([
        'cv_section_id' => $section->id,
        'job_title' => 'Senior Developer',
        'company_name' => 'Company A',
        'start_date' => '2020-01-01',
        'highlights' => ['Achievement 1', 'Achievement 2'],
        'display_order' => 2,
    ]);
    CvExperience::create([
        'cv_section_id' => $section->id,
        'job_title' => 'Developer',
        'company_name' => 'Company B',
        'start_date' => '2018-01-01',
        'highlights' => ['Achievement 3'],
        'display_order' => 1,
    ]);

    $experiences = $section->experiences;

    expect($experiences->first()->cvSection())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($experiences->pluck('display_order')->toArray())->toBe([1, 2]);
});

test('cv skill category casts skills as array', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'skills',
        'display_order' => 1,
    ]);

    $category = CvSkillCategory::create([
        'cv_section_id' => $section->id,
        'category_name' => 'Backend',
        'skills' => ['PHP', 'Laravel', 'MySQL'],
        'display_order' => 1,
    ]);

    expect($category->skills)->toBeArray();
    expect($category->skills)->toBe(['PHP', 'Laravel', 'MySQL']);
});

test('cv experience casts highlights as array', function () {
    $cv = Cv::factory()->create();
    $section = CvSection::create([
        'cv_id' => $cv->id,
        'section_type' => 'experience',
        'display_order' => 1,
    ]);

    $experience = CvExperience::create([
        'cv_section_id' => $section->id,
        'job_title' => 'Developer',
        'company_name' => 'Company',
        'start_date' => '2020-01-01',
        'highlights' => ['Built feature X', 'Improved performance by 50%'],
        'display_order' => 1,
    ]);

    expect($experience->highlights)->toBeArray();
    expect($experience->highlights)->toBe(['Built feature X', 'Improved performance by 50%']);
});
