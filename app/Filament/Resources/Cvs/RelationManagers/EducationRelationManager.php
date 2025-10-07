<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use App\Models\CvEducation;
use App\Models\CvSection;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EducationRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Education';

    protected function getTableQuery(): Builder
    {
        $section = CvSection::firstOrCreate([
            'cv_id' => $this->getOwnerRecord()->id,
            'section_type' => 'education',
        ], [
            'display_order' => 5,
        ]);

        return CvEducation::query()->where('cv_section_id', $section->id);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('degree'),
                TextColumn::make('institution'),
                TextColumn::make('start_year'),
                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->reorderable('display_order')
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('degree')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('institution')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('start_year')
                            ->numeric()
                            ->required()
                            ->minValue(1900)
                            ->maxValue(2100),
                        TextInput::make('end_year')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(2100),
                        Textarea::make('description')
                            ->rows(3),
                        TextInput::make('display_order')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ])
                    ->using(function (array $data): Model {
                        $section = CvSection::firstOrCreate([
                            'cv_id' => $this->getOwnerRecord()->id,
                            'section_type' => 'education',
                        ], [
                            'display_order' => 5,
                        ]);

                        $data['cv_section_id'] = $section->id;

                        return CvEducation::create($data);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        TextInput::make('degree')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('institution')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('start_year')
                            ->numeric()
                            ->required()
                            ->minValue(1900)
                            ->maxValue(2100),
                        TextInput::make('end_year')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(2100),
                        Textarea::make('description')
                            ->rows(3),
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
