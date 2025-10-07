<?php

namespace App\Filament\Resources\PDFSnapshots\Pages;

use App\Filament\Resources\PDFSnapshots\PDFSnapshotResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPDFSnapshot extends ViewRecord
{
    protected static string $resource = PDFSnapshotResource::class;

    protected ?string $heading = 'View PDF Snapshot';

    protected function getHeaderActions(): array
    {
        return [
            // No actions - PDF Snapshots are read-only
        ];
    }
}
