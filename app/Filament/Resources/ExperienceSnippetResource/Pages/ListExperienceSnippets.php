<?php

namespace App\Filament\Resources\ExperienceSnippetResource\Pages;

use App\Filament\Resources\ExperienceSnippetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExperienceSnippets extends ListRecords
{
    protected static string $resource = ExperienceSnippetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Snippet')
                ->icon('heroicon-o-plus'),
        ];
    }
}
