<?php

namespace App\Filament\Resources\PdfTemplates\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PdfTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->readOnly(),

                Textarea::make('description')
                    ->maxLength(500)
                    ->rows(3),

                TextInput::make('view_path')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('cv.templates.modern')
                    ->helperText('Blade view path (e.g., cv.templates.modern)'),

                FileUpload::make('preview_image_path')
                    ->label('Preview Image')
                    ->image()
                    ->disk('public')
                    ->directory('template-previews')
                    ->maxSize(2048)
                    ->helperText('Upload a preview image (max 2MB)'),

                Toggle::make('is_default')
                    ->label('Default Template')
                    ->helperText('Only one template can be set as default'),
            ]);
    }
}
