<?php

namespace App\Filament\Resources\CVVersions\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CVVersionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Version Details')
                    ->description('Create a snapshot of a CV at a specific point in time')
                    ->icon('heroicon-o-document-duplicate')
                    ->schema([
                        Select::make('cv_id')
                            ->label('CV to Version')
                            ->relationship('cv', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // Load the CV and convert to JSON
                                    $cv = \App\Models\Cv::with([
                                        'headerInfo',
                                        'sections.summary',
                                        'sections.skillCategories',
                                        'sections.experiences',
                                        'sections.projects',
                                        'sections.education',
                                        'sections.reference',
                                        'sections.customSection',
                                    ])->find($state);

                                    if ($cv) {
                                        $set('snapshot_json', json_encode($cv->toArray(), JSON_PRETTY_PRINT));
                                    }
                                }
                            })
                            ->helperText('Select the CV you want to create a version snapshot of. The snapshot will be auto-generated.'),

                        TextInput::make('reason')
                            ->label('Version Reason')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Before applying to Acme Corp, Updated experience section')
                            ->helperText('Brief description of why this version was created'),

                        Hidden::make('snapshot_json')
                            ->default('{}'),
                    ]),
            ]);
    }
}
