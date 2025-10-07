<?php

namespace App\Filament\Resources\CVVersions\Pages;

use App\Filament\Resources\CVVersions\CVVersionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCVVersions extends ListRecords
{
    protected static string $resource = CVVersionResource::class;

    protected ?string $heading = 'CV Versions';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
