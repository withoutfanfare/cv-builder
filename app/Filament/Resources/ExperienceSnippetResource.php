<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExperienceSnippetResource\Pages;
use App\Models\ExperienceSnippet;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExperienceSnippetResource extends Resource
{
    protected static ?string $model = ExperienceSnippet::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Snippet Library';

    protected static ?string $navigationGroup = 'CV Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Snippet Details')
                    ->description('Create reusable achievement bullets for faster CV building')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        TextInput::make('title')
                            ->label('Snippet Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Led cross-functional team...')
                            ->helperText('A brief title to identify this snippet'),

                        Textarea::make('content')
                            ->label('Achievement Bullet')
                            ->required()
                            ->rows(3)
                            ->placeholder('Led a cross-functional team of 8 engineers to deliver a microservices architecture migration, reducing deployment time by 60% and improving system reliability to 99.9% uptime.')
                            ->helperText('The full achievement bullet text')
                            ->columnSpanFull(),
                    ]),

                Section::make('Categorization')
                    ->description('Tag your snippets for easy filtering and AI-powered suggestions')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        Select::make('category')
                            ->label('Category')
                            ->options([
                                'leadership' => 'Leadership',
                                'technical' => 'Technical Achievement',
                                'project_management' => 'Project Management',
                                'collaboration' => 'Collaboration',
                                'problem_solving' => 'Problem Solving',
                                'innovation' => 'Innovation',
                                'optimization' => 'Optimization',
                                'mentorship' => 'Mentorship',
                                'communication' => 'Communication',
                                'other' => 'Other',
                            ])
                            ->searchable()
                            ->native(false)
                            ->placeholder('Select a category'),

                        Select::make('role_type')
                            ->label('Role Type')
                            ->options([
                                'engineer' => 'Software Engineer',
                                'senior_engineer' => 'Senior Engineer',
                                'lead_engineer' => 'Lead Engineer',
                                'architect' => 'Architect',
                                'manager' => 'Engineering Manager',
                                'product_manager' => 'Product Manager',
                                'designer' => 'Designer',
                                'data_scientist' => 'Data Scientist',
                                'devops' => 'DevOps Engineer',
                                'qa' => 'QA Engineer',
                                'other' => 'Other',
                            ])
                            ->searchable()
                            ->native(false)
                            ->placeholder('Which role is this for?'),

                        TagsInput::make('tags')
                            ->label('Skills & Keywords')
                            ->placeholder('React, TypeScript, AWS...')
                            ->helperText('Add skills and keywords for AI matching')
                            ->columnSpanFull(),

                        Select::make('industry')
                            ->label('Industry')
                            ->options([
                                'tech' => 'Technology',
                                'finance' => 'Finance',
                                'healthcare' => 'Healthcare',
                                'ecommerce' => 'E-commerce',
                                'saas' => 'SaaS',
                                'consulting' => 'Consulting',
                                'education' => 'Education',
                                'other' => 'Other',
                            ])
                            ->searchable()
                            ->native(false)
                            ->placeholder('Select an industry'),
                    ]),

                Section::make('Usage Statistics')
                    ->description('Track how often this snippet is used')
                    ->icon('heroicon-o-chart-bar')
                    ->columns(2)
                    ->schema([
                        TextInput::make('usage_count')
                            ->label('Times Used')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('last_used_at')
                            ->label('Last Used')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => $state ? $state->diffForHumans() : 'Never'),
                    ])
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('content')
                    ->searchable()
                    ->limit(60)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->content),

                TextColumn::make('category')
                    ->badge()
                    ->colors([
                        'primary' => 'leadership',
                        'success' => 'technical',
                        'warning' => 'project_management',
                        'info' => 'collaboration',
                        'danger' => 'problem_solving',
                    ])
                    ->sortable()
                    ->searchable(),

                TagsColumn::make('tags')
                    ->limit(3)
                    ->searchable(),

                TextColumn::make('role_type')
                    ->label('Role')
                    ->badge()
                    ->sortable(),

                TextColumn::make('usage_count')
                    ->label('Uses')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                TextColumn::make('last_used_at')
                    ->label('Last Used')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('Never'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'leadership' => 'Leadership',
                        'technical' => 'Technical Achievement',
                        'project_management' => 'Project Management',
                        'collaboration' => 'Collaboration',
                        'problem_solving' => 'Problem Solving',
                        'innovation' => 'Innovation',
                        'optimization' => 'Optimization',
                        'mentorship' => 'Mentorship',
                        'communication' => 'Communication',
                    ]),

                SelectFilter::make('role_type')
                    ->label('Role Type')
                    ->options([
                        'engineer' => 'Software Engineer',
                        'senior_engineer' => 'Senior Engineer',
                        'lead_engineer' => 'Lead Engineer',
                        'architect' => 'Architect',
                        'manager' => 'Engineering Manager',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('usage_count', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExperienceSnippets::route('/'),
            'create' => Pages\CreateExperienceSnippet::route('/create'),
            'edit' => Pages\EditExperienceSnippet::route('/{record}/edit'),
        ];
    }
}
