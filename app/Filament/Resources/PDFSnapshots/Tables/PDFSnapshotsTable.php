<?php

namespace App\Filament\Resources\PDFSnapshots\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PDFSnapshotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jobApplication.company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jobApplication.job_title')
                    ->label('Job Title')
                    ->searchable(),
                TextColumn::make('cv.title')
                    ->label('CV')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('file_path')
                    ->label('File')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('hash')
                    ->label('Hash')
                    ->limit(10)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
