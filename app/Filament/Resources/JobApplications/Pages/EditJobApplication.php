<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Jobs\ProcessCvReview;
use App\Services\CvReviewService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditJobApplication extends EditRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // T026: Review CV Action
            Action::make('reviewCv')
                ->label('Review CV')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->visible(fn ($record) => $record->send_status === 'draft' && ! empty($record->job_description) && strlen($record->job_description) >= 50 && ! $record->ai_review_completed_at)
                ->requiresConfirmation()
                ->modalHeading('Request AI CV Review')
                ->modalDescription(function ($record) {
                    $service = app(CvReviewService::class);
                    $estimatedTokens = $service->estimateTokenCount($record->cv, $record);
                    $estimatedCost = $service->estimateCostCents($estimatedTokens);

                    return 'This will analyze your CV against the job description. Estimated cost: $'.number_format($estimatedCost / 100, 2).'. Reviews typically complete in 5-10 seconds.';
                })
                ->action(function ($record) {
                    $record->update(['ai_review_requested_at' => now()]);

                    ProcessCvReview::dispatch($record);

                    Notification::make()
                        ->title('Review Queued')
                        ->body('Your CV review has been queued and will complete shortly.')
                        ->success()
                        ->send();
                }),

            Action::make('regenerateReview')
                ->label('Regenerate Review')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn ($record) => $record->ai_review_completed_at && $record->send_status === 'draft')
                ->requiresConfirmation()
                ->modalHeading('Regenerate AI CV Review')
                ->modalDescription('This will create a new review and overwrite the existing one. You will be charged for the new review.')
                ->action(function ($record) {
                    $record->update(['ai_review_requested_at' => now()]);

                    ProcessCvReview::dispatch($record);

                    Notification::make()
                        ->title('Review Regenerating')
                        ->body('Your CV review is being regenerated.')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }

    // T027 & T028: Review Results Display - Add to bottom of edit form
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // This ensures the review data is available
        return $data;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                Section::make('AI CV Review')
                    ->description(fn ($record) => $record->ai_review_completed_at ? 'Last updated '.($record->ai_review_completed_at->diffForHumans() ?? 'recently') : null)
                    ->icon('heroicon-o-sparkles')
                    ->collapsed(fn ($record) => ! $record->ai_review_completed_at)
                    ->visible(fn ($record) => $record->ai_review_completed_at || $record->ai_review_requested_at)
                    ->headerActions([
                        InfolistAction::make('regenerate')
                            ->label('Regenerate')
                            ->icon('heroicon-o-arrow-path')
                            ->color('warning')
                            ->visible(fn ($record) => $record->ai_review_completed_at)
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $record->update(['ai_review_requested_at' => now()]);
                                ProcessCvReview::dispatch($record);

                                Notification::make()
                                    ->title('Review Regenerating')
                                    ->body('Your CV review is being regenerated.')
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->schema([
                        // Stale review warning (T028)
                        ViewEntry::make('stale_warning')
                            ->label('')
                            ->visible(fn ($record) => $record->isReviewStale())
                            ->view('filament.infolists.stale-review-alert'),

                        // In progress indicator
                        TextEntry::make('ai_review_requested_at')
                            ->label('Review Status')
                            ->visible(fn ($record) => $record->ai_review_requested_at && ! $record->ai_review_completed_at)
                            ->formatStateUsing(fn () => '⏳ Review in progress...')
                            ->color('warning'),

                        // Match score and results
                        Split::make([
                            TextEntry::make('ai_review_data.match_score')
                                ->label('Match Score')
                                ->visible(fn ($record) => $record->ai_review_completed_at)
                                ->badge()
                                ->size('lg')
                                ->color(fn ($state) => match (true) {
                                    $state >= 70 => 'success',
                                    $state >= 50 => 'warning',
                                    default => 'danger',
                                })
                                ->formatStateUsing(fn ($state) => $state.'%'),

                            TextEntry::make('ai_review_cost_cents')
                                ->label('Cost')
                                ->visible(fn ($record) => $record->ai_review_completed_at)
                                ->money('USD', divideBy: 100)
                                ->color('gray'),
                        ])->visible(fn ($record) => $record->ai_review_completed_at),

                        Tabs::make('ReviewTabs')
                            ->visible(fn ($record) => $record->ai_review_completed_at)
                            ->tabs([
                                Tabs\Tab::make('Skill Gaps')
                                    ->icon('heroicon-o-academic-cap')
                                    ->badge(fn ($record) => count($record->ai_review_data['skill_gaps'] ?? []))
                                    ->schema([
                                        ViewEntry::make('skill_gaps')
                                            ->label('')
                                            ->view('filament.infolists.skill-gaps'),
                                    ]),

                                Tabs\Tab::make('Section Priority')
                                    ->icon('heroicon-o-bars-3')
                                    ->badge(fn ($record) => count($record->ai_review_data['section_recommendations'] ?? []))
                                    ->schema([
                                        ViewEntry::make('section_recommendations')
                                            ->label('')
                                            ->view('filament.infolists.section-recommendations'),
                                    ]),

                                Tabs\Tab::make('Bullet Points')
                                    ->icon('heroicon-o-list-bullet')
                                    ->badge(fn ($record) => count($record->ai_review_data['bullet_improvements'] ?? []))
                                    ->schema([
                                        ViewEntry::make('bullet_improvements')
                                            ->label('')
                                            ->view('filament.infolists.bullet-improvements'),
                                    ]),

                                Tabs\Tab::make('Language')
                                    ->icon('heroicon-o-language')
                                    ->badge(fn ($record) => count($record->ai_review_data['language_suggestions'] ?? []))
                                    ->schema([
                                        ViewEntry::make('language_suggestions')
                                            ->label('')
                                            ->view('filament.infolists.language-suggestions'),
                                    ]),

                                Tabs\Tab::make('Skill Evidence')
                                    ->icon('heroicon-o-check-badge')
                                    ->badge(fn ($record) => count($record->ai_review_data['skill_evidence'] ?? []))
                                    ->schema([
                                        ViewEntry::make('skill_evidence')
                                            ->label('')
                                            ->view('filament.infolists.skill-evidence'),
                                    ]),
                            ]),

                        // Action Checklist
                        ViewEntry::make('action_checklist')
                            ->label('Action Items')
                            ->visible(fn ($record) => $record->ai_review_completed_at && ! empty($record->ai_review_data['action_checklist']))
                            ->view('filament.infolists.action-checklist'),
                    ]),
            ]);
    }
}
