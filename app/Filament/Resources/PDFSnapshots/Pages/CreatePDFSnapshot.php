<?php

namespace App\Filament\Resources\PDFSnapshots\Pages;

use App\Filament\Resources\PDFSnapshots\PDFSnapshotResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePDFSnapshot extends CreateRecord
{
    protected static string $resource = PDFSnapshotResource::class;
}
