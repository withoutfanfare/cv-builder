<?php

namespace App\Filament\Pages;

use App\Jobs\ProcessBatchAnalysis;
use App\Models\BatchAnalysis;
use App\Models\Cv;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class BatchJobAnalysis extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static string $view = 'filament.pages.batch-job-analysis';

    protected static ?string $navigationLabel = 'Batch Analysis';

    protected static ?string $title = 'Batch Job Analysis';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Create New Batch Analysis')
                    ->description('Compare your CV against multiple job descriptions at once')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        TextInput::make('name')
                            ->label('Analysis Name')
                            ->required()
                            ->placeholder('Tech Companies - Senior Engineer Roles')
                            ->helperText('Give this batch a descriptive name'),

                        Select::make('cv_id')
                            ->label('CV to Analyze')
                            ->options(Cv::all()->pluck('title', 'id'))
                            ->required()
                            ->searchable()
                            ->native(false),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->placeholder('Comparing positions at FAANG companies...')
                            ->helperText('Optional notes about this batch'),

                        Repeater::make('job_descriptions')
                            ->label('Job Descriptions')
                            ->schema([
                                TextInput::make('company_name')
                                    ->label('Company')
                                    ->required()
                                    ->placeholder('Google'),

                                TextInput::make('job_title')
                                    ->label('Job Title')
                                    ->required()
                                    ->placeholder('Senior Software Engineer'),

                                Textarea::make('description')
                                    ->label('Job Description')
                                    ->required()
                                    ->rows(6)
                                    ->placeholder('Paste the full job description here...')
                                    ->minLength(200),
                            ])
                            ->minItems(2)
                            ->maxItems(10)
                            ->defaultItems(2)
                            ->addActionLabel('Add Another Job')
                            ->collapsible()
                            ->itemLabel(fn ($state) => ($state['company_name'] ?? 'New Job').' - '.($state['job_title'] ?? 'Position'))
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(BatchAnalysis::query()->latest())
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('cv.title')
                    ->label('CV')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
                    ->sortable(),

                TextColumn::make('job_count')
                    ->label('Jobs')
                    ->getStateUsing(fn ($record) => count($record->job_descriptions ?? []))
                    ->alignCenter(),

                TextColumn::make('average_score')
                    ->label('Avg Score')
                    ->getStateUsing(fn ($record) => $record->isComplete() ? round($record->getAverageScore()).'%' : '-')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        ! $record->isComplete() => 'gray',
                        $record->getAverageScore() >= 70 => 'success',
                        $record->getAverageScore() >= 50 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('total_cost_cents')
                    ->label('Cost')
                    ->money('USD', divideBy: 100)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn ($record) => $record->name)
                    ->modalContent(fn ($record) => view('filament.pages.components.batch-analysis-results', ['record' => $record])),

                Action::make('rerun')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record) => $record->hasFailed())
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'pending']);
                        ProcessBatchAnalysis::dispatch($record);

                        Notification::make()
                            ->title('Analysis Restarted')
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No batch analyses yet')
            ->emptyStateDescription('Create your first batch analysis to compare your CV against multiple jobs')
            ->emptyStateIcon('heroicon-o-beaker');
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $batchAnalysis = BatchAnalysis::create([
            'cv_id' => $data['cv_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'job_descriptions' => $data['job_descriptions'],
            'status' => 'pending',
        ]);

        ProcessBatchAnalysis::dispatch($batchAnalysis);

        Notification::make()
            ->title('Batch Analysis Started')
            ->body('Your batch analysis is processing. This may take a few minutes.')
            ->success()
            ->duration(5000)
            ->send();

        $this->form->fill();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('create')
                ->label('Start Batch Analysis')
                ->icon('heroicon-o-play')
                ->action('create')
                ->color('primary')
                ->size('lg'),
        ];
    }
}
