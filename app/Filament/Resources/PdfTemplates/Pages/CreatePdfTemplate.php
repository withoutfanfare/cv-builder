<?php

namespace App\Filament\Resources\PdfTemplates\Pages;

use App\Filament\Resources\PdfTemplates\PdfTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePdfTemplate extends CreateRecord
{
    protected static string $resource = PdfTemplateResource::class;
}
