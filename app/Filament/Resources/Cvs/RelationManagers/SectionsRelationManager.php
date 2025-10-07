<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $recordTitleAttribute = 'section_type';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('section_type')
                    ->label('Section Type')
                    ->options([
                        'summary' => 'Summary',
                        'skills' => 'Skills',
                        'experience' => 'Experience',
                        'projects' => 'Projects',
                        'education' => 'Education',
                        'references' => 'References',
                    ])
                    ->required(),
                TextInput::make('display_order')
                    ->label('Display Order')
                    ->numeric()
                    ->required()
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('section_type')
                    ->label('Section Type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->reorderable('display_order')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
