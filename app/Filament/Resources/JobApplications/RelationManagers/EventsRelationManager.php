<?php

namespace App\Filament\Resources\JobApplications\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_type')
                    ->required()
                    ->options([
                        'submitted' => 'Submitted',
                        'reply_received' => 'Reply Received',
                        'interview_scheduled' => 'Interview Scheduled',
                        'interview_completed' => 'Interview Completed',
                        'offer_received' => 'Offer Received',
                        'rejected' => 'Rejected',
                        'withdrawn' => 'Withdrawn',
                    ])
                    ->native(false),

                DateTimePicker::make('occurred_at')
                    ->required()
                    ->default(now())
                    ->native(false),

                Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),

                KeyValue::make('metadata')
                    ->keyLabel('Key')
                    ->valueLabel('Value')
                    ->reorderable()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('event_type')
            ->columns([
                TextColumn::make('event_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title())
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'gray',
                        'reply_received' => 'info',
                        'interview_scheduled' => 'warning',
                        'interview_completed' => 'success',
                        'offer_received' => 'success',
                        'rejected' => 'danger',
                        'withdrawn' => 'gray',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('occurred_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->description(fn ($record): string => $record->occurred_at->diffForHumans()),

                TextColumn::make('notes')
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('occurred_at', 'desc');
    }
}
