<?php

use App\Models\Cv;
use App\Models\PdfTemplate;

test('pdf uses selected template', function () {
    // T014: Verify PDF uses the selected template
    $template = PdfTemplate::factory()->create([
        'is_default' => false,
        'view_path' => 'cv.templates.modern',
    ]);
    PdfTemplate::factory()->create(['is_default' => true, 'view_path' => 'cv.templates.default']);

    $cv = Cv::factory()->create([
        'pdf_template_id' => $template->id,
    ]);

    // Load CV with template
    $cv->load('pdfTemplate');

    expect($cv->template->view_path)->toBe('cv.templates.modern');
});

test('pdf uses default when no template', function () {
    // T014: Verify PDF uses default template when none selected
    $defaultTemplate = PdfTemplate::factory()->create([
        'is_default' => true,
        'view_path' => 'cv.templates.default',
    ]);

    $cv = Cv::factory()->create([
        'pdf_template_id' => null,
    ]);

    expect($cv->template->view_path)->toBe('cv.templates.default');
});
