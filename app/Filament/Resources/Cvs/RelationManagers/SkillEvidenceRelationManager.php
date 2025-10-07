<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SkillEvidenceRelationManager extends RelationManager
{
    protected static string $relationship = 'skillEvidence';

    protected static ?string $recordTitleAttribute = 'skill_name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('skill_name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Name of the skill (e.g., "React", "Python")'),
                MorphToSelect::make('evidenceable')
                    ->required()
                    ->types([
                        MorphToSelect\Type::make(\App\Models\CvSection::class)
                            ->titleAttribute('title'),
                        MorphToSelect\Type::make(\App\Models\CvExperience::class)
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->job_title} at {$record->company_name}"),
                        MorphToSelect\Type::make(\App\Models\CvProject::class)
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->project_name),
                    ])
                    ->label('Evidence Source'),
                Textarea::make('notes')
                    ->rows(3)
                    ->helperText('Optional notes about how this evidence demonstrates the skill'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('skill_name')->searchable()->sortable(),
                TextColumn::make('evidenceable_type')
                    ->label('Evidence Type')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
