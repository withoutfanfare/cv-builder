<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use App\Models\CvSection;
use App\Models\CvSummary;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SummaryRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Summary';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('section_type', 'summary')->with('summary')
            )
            ->columns([
                TextColumn::make('summary.content')
                    ->label('Summary')
                    ->limit(100),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        Textarea::make('content')
                            ->required()
                            ->rows(10),
                    ])
                    ->using(function (array $data): Model {
                        $section = CvSection::firstOrCreate([
                            'cv_id' => $this->getOwnerRecord()->id,
                            'section_type' => 'summary',
                        ], [
                            'display_order' => 1,
                        ]);

                        return CvSummary::updateOrCreate([
                            'cv_section_id' => $section->id,
                        ], $data);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        Textarea::make('content')
                            ->required()
                            ->rows(10),
                    ])
                    ->fillForm(fn ($record) => [
                        'content' => $record->summary->content ?? '',
                    ])
                    ->using(function (Model $record, array $data): Model {
                        $record->summary->update($data);

                        return $record;
                    }),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
