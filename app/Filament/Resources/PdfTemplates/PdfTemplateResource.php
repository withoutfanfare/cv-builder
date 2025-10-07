<?php

namespace App\Filament\Resources\PdfTemplates;

use App\Filament\Resources\PdfTemplates\Pages\CreatePdfTemplate;
use App\Filament\Resources\PdfTemplates\Pages\EditPdfTemplate;
use App\Filament\Resources\PdfTemplates\Pages\ListPdfTemplates;
use App\Filament\Resources\PdfTemplates\Schemas\PdfTemplateForm;
use App\Filament\Resources\PdfTemplates\Tables\PdfTemplatesTable;
use App\Models\PdfTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PdfTemplateResource extends Resource
{
    protected static ?string $model = PdfTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'PDF Templates';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return PdfTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PdfTemplatesTable::configure($table);
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
            'index' => ListPdfTemplates::route('/'),
            'create' => CreatePdfTemplate::route('/create'),
            'edit' => EditPdfTemplate::route('/{record}/edit'),
        ];
    }
}
