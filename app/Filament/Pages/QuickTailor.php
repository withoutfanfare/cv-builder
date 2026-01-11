<?php

namespace App\Filament\Pages;

use App\Actions\ApplySuggestionAction;
use App\Models\Cv;
use App\Models\JobApplication;
use App\Models\SectionFocusProfile;
use App\Services\CvReviewService;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class QuickTailor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static string $view = 'filament.pages.quick-tailor';

    protected static ?string $navigationLabel = 'Quick Tailor';

    protected static ?string $title = 'Quick CV Tailor';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public ?array $analysis = null;

    public ?int $selectedCvId = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Select CV & Job')
                        ->description('Choose your base CV and paste the job description')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Select::make('cv_id')
                                ->label('Base CV')
                                ->options(Cv::all()->pluck('title', 'id'))
                                ->required()
                                ->searchable()
                                ->native(false)
                                ->live()
                                ->helperText('Select the CV you want to tailor for this job')
                                ->afterStateUpdated(function ($state) {
                                    $this->selectedCvId = $state;
                                }),

                            TextInput::make('company_name')
                                ->label('Company Name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Acme Corp')
                                ->helperText('The company you\'re applying to'),

                            TextInput::make('job_title')
                                ->label('Job Title')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Senior Software Engineer')
                                ->helperText('The position you\'re applying for'),

                            Textarea::make('job_description')
                                ->label('Job Description')
                                ->rows(12)
                                ->required()
                                ->minLength(200)
                                ->placeholder('Paste the full job description here...')
                                ->helperText('Minimum 200 characters for accurate analysis'),
                        ]),

                    Wizard\Step::make('AI Analysis')
                        ->description('Review AI-generated insights and match score')
                        ->icon('heroicon-o-sparkles')
                        ->schema([
                            Placeholder::make('analyzing')
                                ->label('')
                                ->visible(fn () => $this->analysis === null)
                                ->content(function () {
                                    return view('filament.pages.components.analyzing-indicator');
                                }),

                            Section::make('Match Analysis')
                                ->visible(fn () => $this->analysis !== null)
                                ->schema([
                                    Placeholder::make('match_score')
                                        ->label('Match Score')
                                        ->content(function () {
                                            $score = $this->analysis['match_score'] ?? 0;
                                            $color = $score >= 70 ? 'success' : ($score >= 50 ? 'warning' : 'danger');

                                            return view('filament.pages.components.match-score-display', [
                                                'score' => $score,
                                                'color' => $color,
                                            ]);
                                        }),

                                    Placeholder::make('skill_gaps_summary')
                                        ->label('Skill Gaps Identified')
                                        ->visible(fn () => ! empty($this->analysis['skill_gaps'] ?? []))
                                        ->content(function () {
                                            $gaps = $this->analysis['skill_gaps'] ?? [];
                                            $highPriority = collect($gaps)->where('priority', 'high')->pluck('skill')->join(', ');

                                            return count($gaps).' skill gaps identified'.
                                                   ($highPriority ? " (High priority: {$highPriority})" : '');
                                        }),

                                    Placeholder::make('improvements_summary')
                                        ->label('Improvements Available')
                                        ->content(function () {
                                            $languageSuggestions = count($this->analysis['language_suggestions'] ?? []);
                                            $bulletImprovements = count($this->analysis['bullet_improvements'] ?? []);

                                            return "{$languageSuggestions} language improvements, {$bulletImprovements} bullet point enhancements";
                                        }),
                                ]),
                        ]),

                    Wizard\Step::make('Apply Changes')
                        ->description('Select which improvements to apply')
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            Section::make('Language Improvements')
                                ->description('Select specific language improvements to apply')
                                ->collapsible()
                                ->schema([
                                    CheckboxList::make('selected_language_suggestions')
                                        ->label('')
                                        ->options(function () {
                                            if (! $this->analysis) {
                                                return [];
                                            }

                                            $suggestions = $this->analysis['language_suggestions'] ?? [];
                                            $options = [];

                                            foreach ($suggestions as $index => $suggestion) {
                                                $original = $suggestion['original'] ?? '';
                                                $improvement = $suggestion['improvement'] ?? '';
                                                $priority = $suggestion['priority'] ?? 'medium';

                                                $priorityBadge = match ($priority) {
                                                    'high' => '🔴',
                                                    'medium' => '🟡',
                                                    default => '🟢',
                                                };

                                                $options[$index] = "{$priorityBadge} \"{$original}\" → \"{$improvement}\"";
                                            }

                                            return $options;
                                        })
                                        ->default(function () {
                                            if (! $this->analysis) {
                                                return [];
                                            }

                                            // Pre-select all high priority suggestions
                                            $suggestions = $this->analysis['language_suggestions'] ?? [];
                                            $highPriority = [];

                                            foreach ($suggestions as $index => $suggestion) {
                                                if (($suggestion['priority'] ?? 'medium') === 'high') {
                                                    $highPriority[] = $index;
                                                }
                                            }

                                            return $highPriority;
                                        })
                                        ->columns(1)
                                        ->gridDirection('row'),
                                ]),

                            Checkbox::make('create_section_profile')
                                ->label('Automatically create Section Focus Profile')
                                ->helperText('AI will determine the best section order for this job')
                                ->default(true),

                            Checkbox::make('add_missing_skills')
                                ->label('Add high-priority missing skills to CV')
                                ->helperText('Skills will be added to a "Recommended Skills" category')
                                ->default(false)
                                ->visible(fn () => ! empty($this->analysis['skill_gaps'] ?? [])),
                        ]),

                    Wizard\Step::make('Complete')
                        ->description('Your tailored CV is ready!')
                        ->icon('heroicon-o-check-badge')
                        ->schema([
                            Placeholder::make('success_message')
                                ->label('')
                                ->content(view('filament.pages.components.tailor-complete')),
                        ]),
                ])
                    ->submitAction(view('filament.pages.components.wizard-submit'))
                    ->nextAction(
                        fn (Wizard\Action $action) => $action
                            ->label('Next')
                            ->icon('heroicon-m-arrow-right')
                    )
                    ->previousAction(
                        fn (Wizard\Action $action) => $action
                            ->label('Back')
                            ->icon('heroicon-m-arrow-left')
                    )
                    ->startOnStep(1)
                    ->skippable(false),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('analyze')
                ->label('Analyze with AI')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->visible(fn () => $this->analysis === null)
                ->action(function () {
                    $this->analyzeCV();
                }),
        ];
    }

    protected function analyzeCV(): void
    {
        try {
            $cv = Cv::find($this->data['cv_id']);

            if (! $cv) {
                Notification::make()
                    ->title('Error')
                    ->body('Please select a CV first.')
                    ->danger()
                    ->send();

                return;
            }

            if (empty($this->data['job_description']) || strlen($this->data['job_description']) < 200) {
                Notification::make()
                    ->title('Error')
                    ->body('Job description must be at least 200 characters.')
                    ->danger()
                    ->send();

                return;
            }

            // Create a temporary job application for analysis
            $tempJobApp = new JobApplication([
                'job_description' => $this->data['job_description'],
                'cv_id' => $cv->id,
                'company_name' => $this->data['company_name'] ?? 'Unknown',
                'job_title' => $this->data['job_title'] ?? 'Unknown',
            ]);

            $reviewService = app(CvReviewService::class);
            $this->analysis = $reviewService->analyzeForJob($cv, $tempJobApp);

            Notification::make()
                ->title('Analysis Complete')
                ->body('AI analysis completed successfully!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Analysis Failed')
                ->body('Failed to analyze CV: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function submit(): void
    {
        try {
            DB::beginTransaction();

            $cv = Cv::find($this->data['cv_id']);

            // Apply selected language suggestions
            if (! empty($this->data['selected_language_suggestions'])) {
                $applySuggestionAction = new ApplySuggestionAction;
                $selectedIndices = $this->data['selected_language_suggestions'];
                $allSuggestions = $this->analysis['language_suggestions'] ?? [];

                foreach ($selectedIndices as $index) {
                    if (isset($allSuggestions[$index])) {
                        $applySuggestionAction->applyLanguageSuggestion($cv, $allSuggestions[$index]);
                    }
                }
            }

            // Add missing skills if requested
            if (! empty($this->data['add_missing_skills']) && ! empty($this->analysis['skill_gaps'])) {
                $applySuggestionAction = new ApplySuggestionAction;
                $highPriorityGaps = collect($this->analysis['skill_gaps'])
                    ->where('priority', 'high')
                    ->toArray();
                $applySuggestionAction->addMissingSkills($cv, $highPriorityGaps);
            }

            // Create job application
            $jobApplication = JobApplication::create([
                'cv_id' => $cv->id,
                'company_name' => $this->data['company_name'],
                'job_title' => $this->data['job_title'],
                'job_description' => $this->data['job_description'],
                'send_status' => 'draft',
                'application_status' => 'pending',
                'ai_review_data' => $this->analysis,
                'ai_review_completed_at' => now(),
                'ai_review_cost_cents' => 0, // Free for Quick Tailor
            ]);

            // Create section focus profile if requested
            if (! empty($this->data['create_section_profile'])) {
                $this->createAutoSectionProfile($cv, $jobApplication);
            }

            DB::commit();

            Notification::make()
                ->title('CV Tailored Successfully!')
                ->body('Your tailored CV is ready. View the job application to download the PDF.')
                ->success()
                ->duration(5000)
                ->actions([
                    \Filament\Notifications\Actions\Action::make('view')
                        ->label('View Application')
                        ->url(route('filament.admin.resources.job-applications.edit', ['record' => $jobApplication]))
                        ->button(),
                ])
                ->send();

            // Reset form
            $this->data = [];
            $this->analysis = null;
            $this->selectedCvId = null;
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error')
                ->body('Failed to apply changes: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function createAutoSectionProfile(Cv $cv, JobApplication $jobApplication): void
    {
        // Get section recommendations from AI
        $sectionRecs = $this->analysis['section_recommendations'] ?? [];

        // Determine section order based on AI recommendations
        $allSections = $cv->sections()->get();
        $includedSectionIds = [];
        $sectionOrder = [];

        // High priority sections come first
        foreach ($sectionRecs as $rec) {
            if (($rec['priority'] ?? 'medium') === 'high') {
                $sectionType = $rec['section'] ?? '';
                $section = $allSections->firstWhere('section_type', $sectionType);

                if ($section && ! in_array($section->id, $includedSectionIds)) {
                    $includedSectionIds[] = $section->id;
                    $sectionOrder[] = $section->id;
                }
            }
        }

        // Add remaining sections
        foreach ($allSections as $section) {
            if (! in_array($section->id, $includedSectionIds)) {
                $includedSectionIds[] = $section->id;
                $sectionOrder[] = $section->id;
            }
        }

        // Create the profile
        SectionFocusProfile::create([
            'cv_id' => $cv->id,
            'profile_name' => $jobApplication->company_name.' - '.$jobApplication->job_title,
            'included_section_ids' => $includedSectionIds,
            'section_order' => $sectionOrder,
        ]);
    }
}
