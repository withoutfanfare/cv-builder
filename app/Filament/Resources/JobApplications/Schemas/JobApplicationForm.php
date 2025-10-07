<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use App\Services\KeywordCoverageService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Company & Position')
                    ->description('Basic information about the job opportunity')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(2)
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Acme Corp')
                            ->prefixIcon('heroicon-o-building-office'),

                        TextInput::make('job_title')
                            ->label('Job Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Senior Full-Stack Developer')
                            ->prefixIcon('heroicon-o-briefcase'),

                        TextInput::make('company_website')
                            ->label('Company Website')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://acmecorp.com')
                            ->prefixIcon('heroicon-o-globe-alt'),

                        TextInput::make('source')
                            ->label('Application Source')
                            ->placeholder('LinkedIn, Indeed, Referral, etc.')
                            ->maxLength(100)
                            ->prefixIcon('heroicon-o-magnifying-glass')
                            ->helperText('How did you find this job?'),

                        Textarea::make('company_notes')
                            ->label('Company Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Any notes about the company, culture, team, etc.')
                            ->helperText('Internal notes about the company'),
                    ]),

                Section::make('Contact Information')
                    ->description('Hiring manager or recruiter details')
                    ->icon('heroicon-o-user-group')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('point_of_contact_name')
                            ->label('Contact Name')
                            ->maxLength(255)
                            ->placeholder('Jane Smith')
                            ->prefixIcon('heroicon-o-user'),

                        TextInput::make('point_of_contact_email')
                            ->label('Contact Email')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('jane.smith@acmecorp.com')
                            ->prefixIcon('heroicon-o-envelope'),
                    ]),

                Section::make('Application Status')
                    ->description('Track the current status and key dates')
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->schema([
                        Select::make('application_status')
                            ->label('Application Status')
                            ->options([
                                'pending' => 'Pending',
                                'reviewed' => 'Reviewed',
                                'interviewing' => 'Interviewing',
                                'offered' => 'Offered',
                                'rejected' => 'Rejected',
                                'accepted' => 'Accepted',
                                'withdrawn' => 'Withdrawn',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-flag'),

                        Select::make('send_status')
                            ->label('Send Status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-paper-airplane')
                            ->helperText('Mark as "Sent" to capture PDF snapshot'),

                        DatePicker::make('application_deadline')
                            ->label('Application Deadline')
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar-days')
                            ->displayFormat('d/m/Y'),

                        DatePicker::make('next_action_date')
                            ->label('Next Action Date')
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar')
                            ->displayFormat('d/m/Y')
                            ->helperText('When to follow up or take next action'),
                    ]),

                Section::make('CV Selection')
                    ->description('Choose which CV version to submit')
                    ->icon('heroicon-o-document-duplicate')
                    ->schema([
                        Select::make('cv_id')
                            ->label('CV to Submit')
                            ->relationship('cv', 'title')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->prefixIcon('heroicon-o-document-text')
                            ->helperText('Select the CV version for this application. Change Send Status to "Sent" to capture PDF snapshot.'),
                    ]),

                Section::make('Job Description')
                    ->description('Full job posting text for keyword analysis')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->schema([
                        RichEditor::make('job_description')
                            ->label('Job Description')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'italic',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->placeholder('Paste the full job description here for keyword analysis'),

                        Placeholder::make('keyword_coverage')
                            ->label('Keyword Coverage Analysis')
                            ->content(function ($get) {
                                $jobDescription = $get('job_description');
                                $cvId = $get('cv_id');

                                if (! $jobDescription || ! $cvId) {
                                    return 'Add a job description and select a CV to see keyword coverage.';
                                }

                                $cv = \App\Models\Cv::find($cvId);
                                if (! $cv) {
                                    return 'CV not found.';
                                }

                                // Get CV content (simple concatenation of all section titles)
                                $cvContent = $cv->title.' ';
                                foreach ($cv->sections as $section) {
                                    $cvContent .= $section->title.' ';
                                }

                                $service = app(KeywordCoverageService::class);
                                $coverage = $service->calculateCoverage($jobDescription, $cvContent);

                                $percentage = $coverage['coverage_percentage'];
                                $color = $percentage >= 70 ? 'success' : ($percentage >= 40 ? 'warning' : 'danger');
                                $missingKeywords = implode(', ', array_slice($coverage['missing_keywords'], 0, 10));

                                return "Coverage: {$percentage}% ({$coverage['matched_count']}/{$coverage['total_job_keywords']} keywords)\n".
                                       "Top missing keywords: {$missingKeywords}";
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Interview Details')
                    ->description('Track interview dates and preparation')
                    ->icon('heroicon-o-users')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('interview_dates')
                            ->label('Interview Schedule')
                            ->schema([
                                DateTimePicker::make('date')
                                    ->label('Date & Time')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false),
                                TextInput::make('type')
                                    ->label('Interview Type')
                                    ->placeholder('e.g., Phone Screen, Technical Interview, Final Round')
                                    ->maxLength(255),
                            ])
                            ->columnSpanFull()
                            ->collapsible()
                            ->defaultItems(0)
                            ->addActionLabel('Add Interview')
                            ->reorderable(false),

                        Textarea::make('interview_notes')
                            ->label('Interview Preparation Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Questions to ask, topics to research, etc.')
                            ->helperText('Use this space for interview prep and post-interview reflections'),
                    ]),

                Section::make('Additional Notes')
                    ->description('Any other relevant information')
                    ->icon('heroicon-o-pencil-square')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('notes')
                            ->label('General Notes')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Any other notes about this application...'),
                    ]),

                Section::make('AI CV Review Summary')
                    ->description(fn ($record) => $record?->ai_review_completed_at ? 'Last updated '.$record->ai_review_completed_at->diffForHumans() : null)
                    ->icon('heroicon-o-sparkles')
                    ->collapsible()
                    ->collapsed(fn ($record) => ! $record?->ai_review_completed_at)
                    ->visible(fn ($record) => $record?->ai_review_completed_at || $record?->ai_review_requested_at)
                    ->schema([
                        ViewField::make('review_summary')
                            ->view('filament.forms.ai-review-summary')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
