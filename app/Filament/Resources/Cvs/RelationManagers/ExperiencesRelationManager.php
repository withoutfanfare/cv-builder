<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExperiencesRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Experience';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('job_title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('company_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('location')
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->hidden(fn ($get) => $get('is_current')),
                Checkbox::make('is_current')
                    ->label('Currently working here')
                    ->reactive(),
                Repeater::make('highlights')
                    ->label('Highlights / Achievements')
                    ->simple(
                        Textarea::make('highlight')
                            ->required()
                            ->rows(2)
                    )
                    ->required()
                    ->minItems(1),
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
            ->modifyQueryUsing(fn (Builder $query) => $query->where('section_type', 'experience')
                ->with('experiences')
            )
            ->columns([
                TextColumn::make('experiences.job_title')
                    ->label('Job Title'),
                TextColumn::make('experiences.company_name')
                    ->label('Company'),
                TextColumn::make('experiences.start_date')
                    ->label('Period')
                    ->formatStateUsing(function ($record) {
                        if (! $record->experiences->first()) {
                            return '';
                        }
                        $exp = $record->experiences->first();
                        $start = $exp->start_date->format('M Y');
                        $end = $exp->is_current ? 'Present' : ($exp->end_date ? $exp->end_date->format('M Y') : '');

                        return "{$start} - {$end}";
                    }),
            ])
            ->defaultSort('display_order')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
