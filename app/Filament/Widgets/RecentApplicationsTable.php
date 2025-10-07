<?php

namespace App\Filament\Widgets;

use App\Models\JobApplication;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentApplicationsTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JobApplication::query()
                    ->with(['cv'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cv.title')
                    ->label('CV Used')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('send_status')
                    ->label('Send Status')
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'sent',
                    ]),

                Tables\Columns\BadgeColumn::make('application_status')
                    ->label('Application Status')
                    ->colors([
                        'gray' => 'pending',
                        'info' => 'reviewed',
                        'warning' => 'interviewing',
                        'success' => 'offered',
                        'danger' => 'rejected',
                        'primary' => 'accepted',
                        'secondary' => 'withdrawn',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ]);
    }
}
