<?php

namespace App\Filament\Resources\Cvs\Tables;

use App\Filament\Resources\Cvs\CvResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CvsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('headerInfo.full_name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label('Status')
                    ->placeholder('Active CVs')
                    ->trueLabel('Archived CVs')
                    ->falseLabel('Active CVs')
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('clone')
                    ->label('Clone CV')
                    ->icon('heroicon-o-document-duplicate')
                    ->requiresConfirmation()
                    ->modalHeading('Clone CV')
                    ->modalDescription('This will create a full copy of this CV with all sections, skills, and highlights. A version snapshot will be created for tracking.')
                    ->action(function ($record) {
                        $clonedCv = $record->cloneCv('Cloned via Filament');

                        Notification::make()
                            ->success()
                            ->title('CV cloned successfully')
                            ->body("Created '{$clonedCv->title}' with version snapshot")
                            ->send();

                        return redirect(CvResource::getUrl('edit', ['record' => $clonedCv]));
                    })
                    ->color('success')
                    ->size('sm'),
                Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn ($record) => route('cv.pdf', $record))
                    ->openUrlInNewTab()
                    ->size('sm'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
