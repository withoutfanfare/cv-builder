<?php

namespace Database\Seeders;

use App\Models\PdfTemplate;
use Illuminate\Database\Seeder;

class PdfTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Default',
                'slug' => 'default',
                'description' => 'The classic CV template with traditional layout',
                'view_path' => 'cv.templates.default',
                'preview_image_path' => 'template-previews/default.png',
                'is_default' => true,
            ],
            [
                'name' => 'Modern',
                'slug' => 'modern',
                'description' => 'A modern template with clean design and color accents',
                'view_path' => 'cv.templates.modern',
                'preview_image_path' => 'template-previews/modern.png',
                'is_default' => false,
            ],
            [
                'name' => 'Classic',
                'slug' => 'classic',
                'description' => 'A traditional template with serif fonts and minimal styling',
                'view_path' => 'cv.templates.classic',
                'preview_image_path' => 'template-previews/classic.png',
                'is_default' => false,
            ],
        ];

        foreach ($templates as $template) {
            PdfTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
