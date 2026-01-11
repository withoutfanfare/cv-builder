<?php

namespace App\Filament\Resources\ExperienceSnippetResource\Pages;

use App\Filament\Resources\ExperienceSnippetResource;
use App\Models\Cv;
use App\Services\SnippetGenerationService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListExperienceSnippets extends ListRecords
{
    protected static string $resource = ExperienceSnippetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulk_generate')
                ->label('AI Generate from CVs')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->modalHeading('Bulk Generate Snippets from CVs')
                ->modalDescription('Select CVs to automatically extract and categorize achievement bullets into reusable snippets.')
                ->modalWidth('3xl')
                ->form([
                    CheckboxList::make('cv_ids')
                        ->label('Select CVs')
                        ->options(Cv::all()->pluck('title', 'id'))
                        ->required()
                        ->searchable()
                        ->bulkToggleable()
                        ->columns(2)
                        ->helperText('AI will analyze all experiences from selected CVs'),

                    Checkbox::make('skip_duplicates')
                        ->label('Skip duplicate snippets')
                        ->helperText('Avoid creating snippets with similar content')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $service = new SnippetGenerationService();
                    $result = $service->bulkGenerate($data['cv_ids'], true);

                    Notification::make()
                        ->title('Snippets Generated!')
                        ->body("Generated {$result['total_generated']} snippets from {$result['cvs_processed']} CVs.")
                        ->success()
                        ->duration(5000)
                        ->send();

                    if (!empty($result['errors'])) {
                        Notification::make()
                            ->title('Some snippets had errors')
                            ->body(count($result['errors']) . ' snippets could not be generated.')
                            ->warning()
                            ->send();
                    }

                    // Refresh the table
                    $this->dispatch('$refresh');
                })
                ->visible(fn () => Cv::count() > 0),

            CreateAction::make()
                ->label('Create Snippet')
                ->icon('heroicon-o-plus'),
        ];
    }
}
