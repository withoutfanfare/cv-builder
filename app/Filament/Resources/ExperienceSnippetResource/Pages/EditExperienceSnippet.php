<?php

namespace App\Filament\Resources\ExperienceSnippetResource\Pages;

use App\Filament\Resources\ExperienceSnippetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExperienceSnippet extends EditRecord
{
    protected static string $resource = ExperienceSnippetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
