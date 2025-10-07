<?php

namespace App\Filament\Resources\PdfTemplates\Pages;

use App\Filament\Resources\PdfTemplates\PdfTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPdfTemplate extends EditRecord
{
    protected static string $resource = PdfTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action) {
                    if ($this->record->is_default) {
                        $action->cancel();
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Cannot delete default template')
                            ->body('Please set another template as default first.')
                            ->send();
                    }
                }),
        ];
    }
}
