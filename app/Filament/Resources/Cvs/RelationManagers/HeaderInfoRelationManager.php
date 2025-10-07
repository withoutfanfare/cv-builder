<?php

namespace App\Filament\Resources\Cvs\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeaderInfoRelationManager extends RelationManager
{
    protected static string $relationship = 'headerInfo';

    protected static ?string $recordTitleAttribute = 'full_name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Basic Information')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('John Smith'),

                        TextInput::make('job_title')
                            ->label('Job Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Senior Full-Stack Developer'),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('john@example.com')
                            ->prefixIcon('heroicon-o-envelope'),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(50)
                            ->placeholder('+44 7700 900000')
                            ->prefixIcon('heroicon-o-phone'),

                        TextInput::make('location')
                            ->label('Location')
                            ->maxLength(255)
                            ->placeholder('London, UK')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->columnSpan(2),
                    ]),

                Section::make('Online Profiles')
                    ->icon('heroicon-o-link')
                    ->collapsed()
                    ->schema([
                        TextInput::make('linkedin_url')
                            ->label('LinkedIn Profile')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://linkedin.com/in/yourprofile')
                            ->prefixIcon('heroicon-o-user-circle'),

                        TextInput::make('github_url')
                            ->label('GitHub Profile')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://github.com/yourusername')
                            ->prefixIcon('heroicon-o-code-bracket'),

                        TextInput::make('website_url')
                            ->label('Personal Website')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://yourwebsite.com')
                            ->prefixIcon('heroicon-o-globe-alt'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->searchable(),
                TextColumn::make('job_title')->searchable(),
                TextColumn::make('email')->searchable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
