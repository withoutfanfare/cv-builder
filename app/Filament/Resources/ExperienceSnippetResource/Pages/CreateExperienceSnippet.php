<?php

namespace App\Filament\Resources\ExperienceSnippetResource\Pages;

use App\Filament\Resources\ExperienceSnippetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExperienceSnippet extends CreateRecord
{
    protected static string $resource = ExperienceSnippetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
