<?php

namespace App\Filament\Resources\PDFSnapshots\Pages;

use App\Filament\Resources\PDFSnapshots\PDFSnapshotResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPDFSnapshot extends EditRecord
{
    protected static string $resource = PDFSnapshotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
