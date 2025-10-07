<?php

namespace App\Filament\Resources\Cvs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CvForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('CV Information')
                    ->description('Basic details about this CV version')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextInput::make('title')
                            ->label('CV Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., John Smith - Senior Developer')
                            ->helperText('Give this CV a descriptive title to easily identify it'),

                        Select::make('pdf_template_id')
                            ->label('PDF Template')
                            ->relationship('pdfTemplate', 'name')
                            ->preload()
                            ->searchable()
                            ->helperText('Choose the design template for your PDF export')
                            ->placeholder('Select a template (or use default)')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return $record->name.($record->is_default ? ' (Default)' : '');
                            }),
                    ]),

                Section::make('Personal Information')
                    ->description('Your contact details that will appear on the CV')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('headerInfo.full_name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('John Smith'),

                        TextInput::make('headerInfo.job_title')
                            ->label('Job Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Senior Full-Stack Developer'),

                        TextInput::make('headerInfo.email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('john@example.com')
                            ->prefixIcon('heroicon-o-envelope'),

                        TextInput::make('headerInfo.phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(50)
                            ->placeholder('+44 7700 900000')
                            ->prefixIcon('heroicon-o-phone'),

                        TextInput::make('headerInfo.location')
                            ->label('Location')
                            ->maxLength(255)
                            ->placeholder('London, UK')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->columnSpan(2),
                    ]),

                Section::make('Online Presence')
                    ->description('Links to your professional profiles and portfolio')
                    ->icon('heroicon-o-link')
                    ->columns(1)
                    ->collapsed()
                    ->schema([
                        TextInput::make('headerInfo.linkedin_url')
                            ->label('LinkedIn Profile')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://linkedin.com/in/yourprofile')
                            ->prefixIcon('heroicon-o-user-circle')
                            ->helperText('Your professional LinkedIn profile URL'),

                        TextInput::make('headerInfo.github_url')
                            ->label('GitHub Profile')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://github.com/yourusername')
                            ->prefixIcon('heroicon-o-code-bracket')
                            ->helperText('Your GitHub profile or portfolio'),

                        TextInput::make('headerInfo.website_url')
                            ->label('Personal Website')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://yourwebsite.com')
                            ->prefixIcon('heroicon-o-globe-alt')
                            ->helperText('Your personal website or portfolio'),
                    ]),
            ]);
    }
}
