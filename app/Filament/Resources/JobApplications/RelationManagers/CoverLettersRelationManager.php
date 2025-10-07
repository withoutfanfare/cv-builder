<?php

namespace App\Filament\Resources\JobApplications\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CoverLettersRelationManager extends RelationManager
{
    protected static string $relationship = 'coverLetters';

    protected static ?string $recordTitleAttribute = 'version';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Textarea::make('template')
                    ->label('Template (with {{variables}})')
                    ->rows(6)
                    ->helperText('Use {{company_name}}, {{role_title}}, etc.'),
                Textarea::make('body')
                    ->required()
                    ->rows(8)
                    ->helperText('Final cover letter text'),
                Select::make('tone')
                    ->required()
                    ->options([
                        'formal' => 'Formal',
                        'casual' => 'Casual',
                        'enthusiastic' => 'Enthusiastic',
                        'technical' => 'Technical',
                        'leadership' => 'Leadership',
                    ])
                    ->default('formal'),
                Toggle::make('is_sent')
                    ->label('Mark as sent')
                    ->helperText('Warning: Sent cover letters cannot be edited'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('version')->sortable(),
                TextColumn::make('tone')->badge(),
                IconColumn::make('is_sent')
                    ->boolean()
                    ->label('Sent'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('version', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->disabled(fn ($record) => $record->is_sent),
                DeleteAction::make()
                    ->disabled(fn ($record) => $record->is_sent),
            ]);
    }
}
