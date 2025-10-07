<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use App\Models\CvExperience;
use App\Models\CvSection;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExperienceRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Experience';

    protected function getTableQuery(): Builder
    {
        $section = CvSection::firstOrCreate([
            'cv_id' => $this->getOwnerRecord()->id,
            'section_type' => 'experience',
        ], [
            'display_order' => 3,
        ]);

        return CvExperience::query()->where('cv_section_id', $section->id);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('job_title'),
                TextColumn::make('company_name'),
                TextColumn::make('start_date')
                    ->date(),
                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->reorderable('display_order')
            ->headerActions([
                CreateAction::make()
                    ->form([
                        Section::make('Position Details')
                            ->icon('heroicon-o-briefcase')
                            ->columns(2)
                            ->schema([
                                TextInput::make('job_title')
                                    ->label('Job Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Senior Developer')
                                    ->columnSpan(1),

                                TextInput::make('company_name')
                                    ->label('Company Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Acme Corp')
                                    ->columnSpan(1),

                                TextInput::make('company_url')
                                    ->label('Company Website')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://company.com')
                                    ->prefixIcon('heroicon-o-globe-alt'),

                                TextInput::make('location')
                                    ->label('Location')
                                    ->maxLength(255)
                                    ->placeholder('London, UK')
                                    ->prefixIcon('heroicon-o-map-pin'),
                            ]),

                        Section::make('Employment Period')
                            ->icon('heroicon-o-calendar')
                            ->columns(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('M Y'),

                                DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->native(false)
                                    ->displayFormat('M Y')
                                    ->hidden(fn ($get) => $get('is_current')),

                                Checkbox::make('is_current')
                                    ->label('Currently working here')
                                    ->reactive()
                                    ->columnSpan(2),
                            ]),

                        Section::make('Key Achievements')
                            ->description('List your major accomplishments and responsibilities')
                            ->icon('heroicon-o-star')
                            ->schema([
                                Repeater::make('highlights')
                                    ->label('Highlights')
                                    ->simple(
                                        Textarea::make('highlight')
                                            ->label('Achievement')
                                            ->required()
                                            ->rows(3)
                                            ->placeholder('Describe a key achievement, using metrics where possible...')
                                    )
                                    ->required()
                                    ->minItems(1)
                                    ->addActionLabel('Add Achievement')
                                    ->collapsible()
                                    ->itemLabel(fn ($state): ?string => $state ? \Illuminate\Support\Str::limit($state, 50) : null),
                            ]),

                        Section::make('Display Order')
                            ->schema([
                                TextInput::make('display_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->helperText('Lower numbers appear first'),
                            ]),
                    ])
                    ->using(function (array $data): Model {
                        $section = CvSection::firstOrCreate([
                            'cv_id' => $this->getOwnerRecord()->id,
                            'section_type' => 'experience',
                        ], [
                            'display_order' => 3,
                        ]);

                        $data['cv_section_id'] = $section->id;

                        return CvExperience::create($data);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        Section::make('Position Details')
                            ->icon('heroicon-o-briefcase')
                            ->columns(2)
                            ->schema([
                                TextInput::make('job_title')
                                    ->label('Job Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Senior Developer')
                                    ->columnSpan(1),

                                TextInput::make('company_name')
                                    ->label('Company Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Acme Corp')
                                    ->columnSpan(1),

                                TextInput::make('company_url')
                                    ->label('Company Website')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://company.com')
                                    ->prefixIcon('heroicon-o-globe-alt'),

                                TextInput::make('location')
                                    ->label('Location')
                                    ->maxLength(255)
                                    ->placeholder('London, UK')
                                    ->prefixIcon('heroicon-o-map-pin'),
                            ]),

                        Section::make('Employment Period')
                            ->icon('heroicon-o-calendar')
                            ->columns(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('M Y'),

                                DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->native(false)
                                    ->displayFormat('M Y')
                                    ->hidden(fn ($get) => $get('is_current')),

                                Checkbox::make('is_current')
                                    ->label('Currently working here')
                                    ->reactive()
                                    ->columnSpan(2),
                            ]),

                        Section::make('Key Achievements')
                            ->description('List your major accomplishments and responsibilities')
                            ->icon('heroicon-o-star')
                            ->schema([
                                Repeater::make('highlights')
                                    ->label('Highlights')
                                    ->simple(
                                        Textarea::make('highlight')
                                            ->label('Achievement')
                                            ->required()
                                            ->rows(3)
                                            ->placeholder('Describe a key achievement, using metrics where possible...')
                                    )
                                    ->required()
                                    ->minItems(1)
                                    ->addActionLabel('Add Achievement')
                                    ->collapsible()
                                    ->itemLabel(fn ($state): ?string => $state ? \Illuminate\Support\Str::limit($state, 50) : null),
                            ]),

                        Section::make('Display Order')
                            ->schema([
                                TextInput::make('display_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->required()
                                    ->helperText('Lower numbers appear first'),
                            ]),
                    ]),
                DeleteAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
