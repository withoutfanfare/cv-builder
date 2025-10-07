<?php

namespace App\Filament\Resources\Cvs\Pages;

use App\Filament\Resources\Cvs\CvResource;
use App\Models\CvHeaderInfo;
use Filament\Resources\Pages\CreateRecord;

class CreateCv extends CreateRecord
{
    protected static string $resource = CvResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract headerInfo data to save after CV is created
        if (isset($data['headerInfo'])) {
            $this->headerInfoData = $data['headerInfo'];
            unset($data['headerInfo']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Save headerInfo after CV is created
        if (isset($this->headerInfoData)) {
            CvHeaderInfo::create([
                'cv_id' => $this->record->id,
                ...$this->headerInfoData,
            ]);
        }
    }
}
