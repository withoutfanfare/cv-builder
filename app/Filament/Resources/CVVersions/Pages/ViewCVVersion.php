<?php

namespace App\Filament\Resources\CVVersions\Pages;

use App\Filament\Resources\CVVersions\CVVersionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCVVersion extends ViewRecord
{
    protected static string $resource = CVVersionResource::class;

    protected ?string $heading = 'View CV Version';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
