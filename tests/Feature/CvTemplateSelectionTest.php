<?php

use App\Models\Cv;
use App\Models\PdfTemplate;

test('can select template for cv', function () {
    // T012: This test will fail until CV form is updated with template selection
    $template = PdfTemplate::factory()->create(['is_default' => false]);
    PdfTemplate::factory()->create(['is_default' => true]); // default template

    $cv = Cv::factory()->create([
        'pdf_template_id' => $template->id,
    ]);

    expect($cv->pdf_template_id)->toBe($template->id);
});

test('template selection persists', function () {
    // T012: This test will fail until CV form is updated
    $template = PdfTemplate::factory()->create(['is_default' => false]);
    PdfTemplate::factory()->create(['is_default' => true]); // default template

    $cv = Cv::factory()->create([
        'pdf_template_id' => $template->id,
    ]);

    $reloadedCv = Cv::find($cv->id);

    expect($reloadedCv->pdf_template_id)->toBe($template->id);
});

test('switching template preserves cv data', function () {
    // T013: This test will fail until template switching works
    $template1 = PdfTemplate::factory()->create(['is_default' => true]);
    $template2 = PdfTemplate::factory()->create(['is_default' => false]);

    $cv = Cv::factory()->create([
        'pdf_template_id' => $template1->id,
        'title' => 'Original Title',
    ]);

    $originalTitle = $cv->title;

    // Switch template
    $cv->update(['pdf_template_id' => $template2->id]);

    $reloadedCv = Cv::find($cv->id);

    expect($reloadedCv->title)->toBe($originalTitle);
    expect($reloadedCv->pdf_template_id)->toBe($template2->id);
});
