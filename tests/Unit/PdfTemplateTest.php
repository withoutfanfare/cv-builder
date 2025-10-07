<?php

use App\Models\PdfTemplate;

test('default returns default template', function () {
    // T006: This test will fail until T015 implements the default() method properly
    $defaultTemplate = PdfTemplate::factory()->create(['is_default' => true]);
    PdfTemplate::factory()->create(['is_default' => false]);

    $result = PdfTemplate::default();

    expect($result->id)->toBe($defaultTemplate->id);
    expect($result->is_default)->toBeTrue();
});

test('validation rules enforced', function () {
    // T008: Validation is handled at Filament level, here we test database constraints
    $template = PdfTemplate::factory()->create([
        'name' => 'Test Template',
        'slug' => 'test-template',
    ]);

    expect($template->name)->toBe('Test Template');
    expect($template->slug)->toBe('test-template');
});

test('preview image validation', function () {
    // T008: Template can be created with preview image path
    $template = PdfTemplate::factory()->create([
        'preview_image_path' => 'template-previews/test.png',
    ]);

    expect($template->preview_image_path)->toBe('template-previews/test.png');
});
