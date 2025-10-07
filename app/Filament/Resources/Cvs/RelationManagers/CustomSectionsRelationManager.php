<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use App\Models\CvCustomSection;
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

class CustomSectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Custom Sections';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('section_type', 'custom')->with('customSection')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customSection.content')
                    ->label('Content')
                    ->limit(100),
                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->reorderable('display_order')
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('content')
                            ->required()
                            ->rows(10),
                        TextInput::make('display_order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(fn () => CvSection::where('cv_id', $this->getOwnerRecord()->id)
                                ->max('display_order') + 1
                            )
                            ->required(),
                    ])
                    ->using(function (array $data): Model {
                        $section = CvSection::create([
                            'cv_id' => $this->getOwnerRecord()->id,
                            'section_type' => 'custom',
                            'title' => $data['title'],
                            'display_order' => $data['display_order'],
                        ]);

                        CvCustomSection::create([
                            'cv_section_id' => $section->id,
                            'content' => $data['content'],
                        ]);

                        return $section;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('content')
                            ->required()
                            ->rows(10),
                        TextInput::make('display_order')
                            ->label('Display Order')
                            ->numeric()
                            ->required(),
                    ])
                    ->fillForm(fn ($record) => [
                        'title' => $record->title,
                        'content' => $record->customSection->content ?? '',
                        'display_order' => $record->display_order,
                    ])
                    ->using(function (Model $record, array $data): Model {
                        $record->update([
                            'title' => $data['title'],
                            'display_order' => $data['display_order'],
                        ]);

                        $record->customSection->update([
                            'content' => $data['content'],
                        ]);

                        return $record;
                    }),
                DeleteAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
