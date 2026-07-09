<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceVersions;

use App\Filament\SuperAdmin\Resources\EvidenceVersions\Pages\CreateEvidenceVersions;
use App\Filament\SuperAdmin\Resources\EvidenceVersions\Pages\EditEvidenceVersions;
use App\Filament\SuperAdmin\Resources\EvidenceVersions\Pages\ListEvidenceVersions;
use App\Filament\SuperAdmin\Resources\EvidenceVersions\Pages\ViewEvidenceVersions;
use App\Filament\SuperAdmin\Resources\EvidenceVersions\Schemas\EvidenceVersionsForm;
use App\Filament\SuperAdmin\Resources\EvidenceVersions\Schemas\EvidenceVersionsInfolist;
use App\Filament\SuperAdmin\Resources\EvidenceVersions\Tables\EvidenceVersionsTable;
use App\Models\EvidenceVersions;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EvidenceVersionsResource extends Resource
{
    protected static ?string $model = EvidenceVersions::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'EvidenceVersions';

    public static function form(Schema $schema): Schema
    {
        return EvidenceVersionsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EvidenceVersionsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvidenceVersionsTable::configure($table);
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
            'index' => ListEvidenceVersions::route('/'),
            'create' => CreateEvidenceVersions::route('/create'),
            'view' => ViewEvidenceVersions::route('/{record}'),
            'edit' => EditEvidenceVersions::route('/{record}/edit'),
        ];
    }
}
