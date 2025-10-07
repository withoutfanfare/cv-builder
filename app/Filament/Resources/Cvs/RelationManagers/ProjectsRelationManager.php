<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use App\Models\CvProject;
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

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Projects';

    protected function getTableQuery(): Builder
    {
        $section = CvSection::firstOrCreate([
            'cv_id' => $this->getOwnerRecord()->id,
            'section_type' => 'projects',
        ], [
            'display_order' => 4,
        ]);

        return CvProject::query()->where('cv_section_id', $section->id);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('project_name'),
                TextColumn::make('description')
                    ->limit(50),
                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->reorderable('display_order')
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('project_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('project_url')
                            ->label('Project URL')
                            ->url()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->required()
                            ->rows(3),
                        Textarea::make('technologies')
                            ->rows(2),
                        TextInput::make('display_order')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ])
                    ->using(function (array $data): Model {
                        $section = CvSection::firstOrCreate([
                            'cv_id' => $this->getOwnerRecord()->id,
                            'section_type' => 'projects',
                        ], [
                            'display_order' => 4,
                        ]);

                        $data['cv_section_id'] = $section->id;

                        return CvProject::create($data);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        TextInput::make('project_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('project_url')
                            ->label('Project URL')
                            ->url()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->required()
                            ->rows(3),
                        Textarea::make('technologies')
                            ->rows(2),
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
