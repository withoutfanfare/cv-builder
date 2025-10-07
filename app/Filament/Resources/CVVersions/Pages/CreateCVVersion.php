<?php

namespace App\Filament\Resources\CVVersions\Pages;

use App\Filament\Resources\CVVersions\CVVersionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCVVersion extends CreateRecord
{
    protected static string $resource = CVVersionResource::class;

    protected ?string $heading = 'Create CV Version';
}
