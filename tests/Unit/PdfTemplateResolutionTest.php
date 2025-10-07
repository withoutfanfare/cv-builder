<?php

use App\Models\Cv;
use App\Models\PdfTemplate;

test('cv template accessor returns selected template', function () {
    // T007: This test will fail until T016 implements the template accessor
    $template = PdfTemplate::factory()->create(['is_default' => false]);
    PdfTemplate::factory()->create(['is_default' => true]); // default template

    $cv = Cv::factory()->create([
        'pdf_template_id' => $template->id,
    ]);

    $result = $cv->template;

    expect($result->id)->toBe($template->id);
});

test('cv template accessor returns default when null', function () {
    // T007: This test will fail until T016 implements the template accessor
    $defaultTemplate = PdfTemplate::factory()->create(['is_default' => true]);

    $cv = Cv::factory()->create([
        'pdf_template_id' => null,
    ]);

    $result = $cv->template;

    expect($result->id)->toBe($defaultTemplate->id);
    expect($result->is_default)->toBeTrue();
});
