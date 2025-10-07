<?php

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\SkillEvidence;

test('can link skill to experience', function () {
    $cv = Cv::factory()->create();
    $experience = CvSection::factory()->create(['cv_id' => $cv->id]);

    $evidence = SkillEvidence::create([
        'cv_id' => $cv->id,
        'skill_name' => 'React',
        'evidenceable_type' => CvSection::class,
        'evidenceable_id' => $experience->id,
        'notes' => 'Built dashboard',
    ]);

    expect($evidence)->toBeInstanceOf(SkillEvidence::class)
        ->and($evidence->skill_name)->toBe('React')
        ->and($evidence->evidenceable_id)->toBe($experience->id)
        ->and($evidence->evidenceable)->toBeInstanceOf(CvSection::class);
});

test('skill names are case insensitive', function () {
    $cv = Cv::factory()->create();
    $experience = CvSection::factory()->create(['cv_id' => $cv->id]);

    SkillEvidence::create([
        'cv_id' => $cv->id,
        'skill_name' => 'React',
        'evidenceable_id' => $experience->id,
        'evidenceable_type' => CvSection::class,
    ]);

    $results = SkillEvidence::where('cv_id', $cv->id)
        ->whereRaw('LOWER(skill_name) = ?', ['react'])
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->skill_name)->toBe('React');
});

test('prevents duplicate skill evidence links', function () {
    $cv = Cv::factory()->create();
    $experience = CvSection::factory()->create(['cv_id' => $cv->id]);

    SkillEvidence::create([
        'cv_id' => $cv->id,
        'skill_name' => 'React',
        'evidenceable_id' => $experience->id,
        'evidenceable_type' => CvSection::class,
    ]);

    expect(fn () => SkillEvidence::create([
        'cv_id' => $cv->id,
        'skill_name' => 'React',
        'evidenceable_type' => CvSection::class,
        'evidenceable_id' => $experience->id,
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

test('deleting cv cascades to evidence', function () {
    $cv = Cv::factory()->create();
    $experience = CvSection::factory()->create(['cv_id' => $cv->id]);

    $evidence = SkillEvidence::create([
        'cv_id' => $cv->id,
        'skill_name' => 'React',
        'evidenceable_id' => $experience->id,
        'evidenceable_type' => CvSection::class,
    ]);

    // Deleting experience leaves orphaned evidence (no cascade on polymorphic)
    $experience->delete();
    expect(SkillEvidence::find($evidence->id))->not->toBeNull();

    // But deleting CV cascades to all evidence
    $cv->forceDelete();
    expect(SkillEvidence::find($evidence->id))->toBeNull();
});

test('aggregates skills with evidence count', function () {
    $cv = Cv::factory()->create();
    $exp1 = CvSection::factory()->create(['cv_id' => $cv->id]);
    $exp2 = CvSection::factory()->create(['cv_id' => $cv->id]);

    SkillEvidence::create([
        'cv_id' => $cv->id,
        'skill_name' => 'React',
        'evidenceable_id' => $exp1->id,
        'evidenceable_type' => CvSection::class,
    ]);

    SkillEvidence::create([
        'cv_id' => $cv->id,
        'skill_name' => 'React',
        'evidenceable_id' => $exp2->id,
        'evidenceable_type' => CvSection::class,
    ]);

    $skillsSummary = $cv->getSkillsWithEvidence();

    expect($skillsSummary)->toHaveKey('React')
        ->and($skillsSummary['React']['evidence_count'])->toBe(2)
        ->and($skillsSummary['React']['evidence_types'])->toContain(CvSection::class);
});
