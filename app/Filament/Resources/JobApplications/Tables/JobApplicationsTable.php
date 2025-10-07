<?php

namespace App\Filament\Resources\JobApplications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('job_title')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('application_status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'info' => 'reviewed',
                        'warning' => 'interviewing',
                        'success' => ['offered', 'accepted'],
                        'danger' => ['rejected', 'withdrawn'],
                    ])
                    ->sortable(),
                TextColumn::make('send_status')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'sent',
                    ])
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('next_action_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->next_action_date && $record->next_action_date->isPast() ? 'danger' : null),
                TextColumn::make('last_activity_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('cv.title')
                    ->label('CV')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('point_of_contact_name')
                    ->label('Contact')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('next_action_date', 'asc')
            ->filters([
                TernaryFilter::make('needs_attention')
                    ->label('Needs Attention')
                    ->queries(
                        true: fn ($query) => $query->needsAttention(),
                        false: fn ($query) => $query->whereNotIn('id', function ($subquery) {
                            $subquery->select('id')->from('job_applications')->needsAttention();
                        }),
                    )
                    ->placeholder('All Applications')
                    ->trueLabel('Needs Attention')
                    ->falseLabel('No Attention Needed'),
                SelectFilter::make('send_status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                    ]),
                SelectFilter::make('application_status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'interviewing' => 'Interviewing',
                        'offered' => 'Offered',
                        'rejected' => 'Rejected',
                        'accepted' => 'Accepted',
                        'withdrawn' => 'Withdrawn',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
