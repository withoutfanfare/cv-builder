<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Models\JobApplication;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ApplicationsNeedingAction extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JobApplication::query()
                    ->needsAttention()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('job_title')
                    ->searchable(),
                TextColumn::make('application_status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'interviewing',
                    ]),
                TextColumn::make('next_action_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->next_action_date && $record->next_action_date->isPast() ? 'danger' : 'warning')
                    ->weight('bold')
                    ->description(fn ($record) => $record->next_action_date && $record->next_action_date->isPast() ? 'OVERDUE' : null),
            ])
            ->recordUrl(fn ($record) => JobApplicationResource::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('No applications need attention right now')
            ->emptyStateDescription('Great job! All your applications are up to date.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
