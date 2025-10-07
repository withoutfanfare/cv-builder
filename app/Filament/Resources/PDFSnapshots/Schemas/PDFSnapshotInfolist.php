<?php

namespace App\Filament\Resources\PDFSnapshots\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PDFSnapshotInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('jobApplication.id')
                    ->label('Job application'),
                TextEntry::make('cv.title')
                    ->label('Cv')
                    ->placeholder('-'),
                TextEntry::make('cvVersion.id')
                    ->label('Cv version')
                    ->placeholder('-'),
                TextEntry::make('file_path'),
                TextEntry::make('hash'),
                TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }
}
