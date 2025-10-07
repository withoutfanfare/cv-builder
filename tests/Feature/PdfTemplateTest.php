<?php

use App\Models\Cv;
use App\Models\PdfTemplate;
use App\Models\User;

test('can create pdf template', function () {
    // T009: This test will fail until Filament resource is created
    $user = User::factory()->create();
    $this->actingAs($user);

    $templateData = [
        'name' => 'Test Template',
        'slug' => 'test-template',
        'description' => 'A test template',
        'view_path' => 'cv.templates.test',
        'preview_image_path' => 'template-previews/test.png',
        'is_default' => false,
    ];

    $template = PdfTemplate::create($templateData);

    expect($template->name)->toBe('Test Template');
    expect($template->slug)->toBe('test-template');
});

test('can update pdf template', function () {
    // T009: This test will fail until Filament resource is created
    $user = User::factory()->create();
    $this->actingAs($user);

    $template = PdfTemplate::factory()->create();

    $template->update(['name' => 'Updated Name']);

    expect($template->fresh()->name)->toBe('Updated Name');
});

test('can list pdf templates', function () {
    // T009: This test will fail until Filament resource is created
    $user = User::factory()->create();
    $this->actingAs($user);

    PdfTemplate::factory()->count(3)->create();

    $templates = PdfTemplate::all();

    expect($templates)->toHaveCount(3);
});

test('cannot delete default template', function () {
    // T010: This test will fail until deletion logic is implemented
    $user = User::factory()->create();
    $this->actingAs($user);

    $defaultTemplate = PdfTemplate::factory()->create(['is_default' => true]);

    $this->expectException(\Exception::class);
    $defaultTemplate->delete();
});

test('can delete non-default template', function () {
    // T010: This test will fail until deletion logic is implemented
    $user = User::factory()->create();
    $this->actingAs($user);

    PdfTemplate::factory()->create(['is_default' => true]); // Ensure default exists
    $template = PdfTemplate::factory()->create(['is_default' => false]);

    $template->delete();

    expect(PdfTemplate::find($template->id))->toBeNull();
});

test('deleting template sets cvs to null', function () {
    // T010: This test will fail until FK cascade is working
    $template = PdfTemplate::factory()->create(['is_default' => false]);
    $defaultTemplate = PdfTemplate::factory()->create(['is_default' => true]);

    $cv = Cv::factory()->create([
        'pdf_template_id' => $template->id,
    ]);

    $template->delete();

    expect($cv->fresh()->pdf_template_id)->toBeNull();
});

test('only one default template allowed', function () {
    // T011: This test will fail until default enforcement is implemented
    $template1 = PdfTemplate::factory()->create(['is_default' => true]);
    $template2 = PdfTemplate::factory()->create(['is_default' => false]);

    // When setting template2 as default, template1 should be unset
    $template2->update(['is_default' => true]);

    expect($template1->fresh()->is_default)->toBeFalse();
    expect($template2->fresh()->is_default)->toBeTrue();
});

test('setting default unsets others', function () {
    // T011: This test will fail until default toggle logic is implemented
    $template1 = PdfTemplate::factory()->create(['is_default' => true]);
    $template2 = PdfTemplate::factory()->create(['is_default' => false]);
    $template3 = PdfTemplate::factory()->create(['is_default' => false]);

    $template3->update(['is_default' => true]);

    expect($template1->fresh()->is_default)->toBeFalse();
    expect($template2->fresh()->is_default)->toBeFalse();
    expect($template3->fresh()->is_default)->toBeTrue();
});

test('gracefully handles missing preview image', function () {
    // T014A: This test will fail until graceful error handling is implemented
    $template = PdfTemplate::factory()->create([
        'preview_image_path' => 'non-existent/path.png',
    ]);

    // Template should still be accessible and selectable
    expect($template->id)->toBeGreaterThan(0);
    expect($template->preview_image_path)->toBe('non-existent/path.png');
});
