<?php

namespace App\Filament\Resources\PDFSnapshots;

use App\Filament\Resources\PDFSnapshots\Pages\ListPDFSnapshots;
use App\Filament\Resources\PDFSnapshots\Pages\ViewPDFSnapshot;
use App\Filament\Resources\PDFSnapshots\Schemas\PDFSnapshotInfolist;
use App\Filament\Resources\PDFSnapshots\Tables\PDFSnapshotsTable;
use App\Models\PDFSnapshot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PDFSnapshotResource extends Resource
{
    protected static ?string $model = PDFSnapshot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static ?string $navigationLabel = 'PDF Snapshots';

    protected static ?string $pluralModelLabel = 'PDF Snapshots';

    protected static ?int $navigationSort = 4;

    public static function infolist(Schema $schema): Schema
    {
        return PDFSnapshotInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PDFSnapshotsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPDFSnapshots::route('/'),
            'view' => ViewPDFSnapshot::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
