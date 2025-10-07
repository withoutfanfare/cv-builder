<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use App\Models\CvReference;
use App\Models\CvSection;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ReferencesRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'References';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('section_type', 'references')->with('reference')
            )
            ->columns([
                TextColumn::make('reference.content')
                    ->label('References')
                    ->limit(100),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        Textarea::make('content')
                            ->required()
                            ->default('Available upon request')
                            ->rows(5),
                    ])
                    ->using(function (array $data): Model {
                        $section = CvSection::firstOrCreate([
                            'cv_id' => $this->getOwnerRecord()->id,
                            'section_type' => 'references',
                        ], [
                            'display_order' => 6,
                        ]);

                        return CvReference::updateOrCreate([
                            'cv_section_id' => $section->id,
                        ], $data);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        Textarea::make('content')
                            ->required()
                            ->rows(5),
                    ])
                    ->fillForm(fn ($record) => [
                        'content' => $record->reference->content ?? '',
                    ])
                    ->using(function (Model $record, array $data): Model {
                        $record->reference->update($data);

                        return $record;
                    }),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
