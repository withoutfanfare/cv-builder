<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use App\Models\CvSection;
use App\Models\CvSkillCategory;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SkillsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Skills';

    protected function getTableQuery(): Builder
    {
        $section = CvSection::firstOrCreate([
            'cv_id' => $this->getOwnerRecord()->id,
            'section_type' => 'skills',
        ], [
            'display_order' => 2,
        ]);

        return CvSkillCategory::query()->where('cv_section_id', $section->id);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('category_name')
                    ->label('Category'),
                TextColumn::make('skills')
                    ->label('Skills')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state),
                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->reorderable('display_order')
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('category_name')
                            ->required()
                            ->maxLength(255),
                        Repeater::make('skills')
                            ->simple(
                                TextInput::make('skill')->required()
                            )
                            ->required()
                            ->minItems(1)
                            ->columns(3),
                        TextInput::make('display_order')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ])
                    ->using(function (array $data): Model {
                        $section = CvSection::firstOrCreate([
                            'cv_id' => $this->getOwnerRecord()->id,
                            'section_type' => 'skills',
                        ], [
                            'display_order' => 2,
                        ]);

                        $data['cv_section_id'] = $section->id;

                        return CvSkillCategory::create($data);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        TextInput::make('category_name')
                            ->required()
                            ->maxLength(255),
                        Repeater::make('skills')
                            ->simple(
                                TextInput::make('skill')->required()
                            )
                            ->required()
                            ->minItems(1)
                            ->columns(3),
                        TextInput::make('display_order')
                            ->numeric()
                            ->required(),
                    ]),
                DeleteAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
