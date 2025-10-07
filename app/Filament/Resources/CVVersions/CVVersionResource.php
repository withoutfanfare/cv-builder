<?php

namespace App\Filament\Resources\CVVersions;

use App\Filament\Resources\CVVersions\Pages\CreateCVVersion;
use App\Filament\Resources\CVVersions\Pages\EditCVVersion;
use App\Filament\Resources\CVVersions\Pages\ListCVVersions;
use App\Filament\Resources\CVVersions\Pages\ViewCVVersion;
use App\Filament\Resources\CVVersions\Schemas\CVVersionForm;
use App\Filament\Resources\CVVersions\Schemas\CVVersionInfolist;
use App\Filament\Resources\CVVersions\Tables\CVVersionsTable;
use App\Models\CVVersion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CVVersionResource extends Resource
{
    protected static ?string $model = CVVersion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $navigationLabel = 'CV Versions';

    protected static ?string $pluralModelLabel = 'CV Versions';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CVVersionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CVVersionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CVVersionsTable::configure($table);
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
            'index' => ListCVVersions::route('/'),
            'create' => CreateCVVersion::route('/create'),
            'view' => ViewCVVersion::route('/{record}'),
            'edit' => EditCVVersion::route('/{record}/edit'),
        ];
    }
}
