<?php

namespace App\Filament\Resources\PdfTemplates\Tables;

use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PdfTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),

                ImageColumn::make('preview_image_path')
                    ->label('Preview')
                    ->disk('public')
                    ->height(50),

                ToggleColumn::make('is_default')
                    ->label('Default')
                    ->disabled(fn ($record) => $record->is_default)
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            // Observer will handle unsetting others
                            $record->update(['is_default' => true]);
                        }
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, $record) {
                        if ($record->is_default) {
                            $action->cancel();
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Cannot delete default template')
                                ->body('Please set another template as default first.')
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }
}
