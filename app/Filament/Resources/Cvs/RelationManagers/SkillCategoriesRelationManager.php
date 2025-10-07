<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use App\Models\CvSection;
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

class SkillCategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Skills';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('category_name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(255),
                Repeater::make('skills')
                    ->label('Skills')
                    ->simple(
                        TextInput::make('skill')
                            ->required()
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
            ->modifyQueryUsing(fn (Builder $query) => $query->where('section_type', 'skills'))
            ->columns([
                TextColumn::make('skillCategories.category_name')
                    ->label('Category'),
                TextColumn::make('skillCategories.skills')
                    ->label('Skills')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state),
                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Create section first if it doesn't exist
                        $section = CvSection::firstOrCreate([
                            'cv_id' => $this->getOwnerRecord()->id,
                            'section_type' => 'skills',
                        ], [
                            'display_order' => CvSection::where('cv_id', $this->getOwnerRecord()->id)->max('display_order') + 1,
                        ]);

                        $data['cv_section_id'] = $section->id;

                        return $data;
                    })
                    ->using(function (array $data, string $model): Model {
                        return $model::create($data);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
