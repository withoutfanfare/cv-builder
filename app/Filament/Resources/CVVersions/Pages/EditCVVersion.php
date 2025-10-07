<?php

namespace App\Filament\Resources\CVVersions\Pages;

use App\Filament\Resources\CVVersions\CVVersionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCVVersion extends EditRecord
{
    protected static string $resource = CVVersionResource::class;

    protected ?string $heading = 'Edit CV Version';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
