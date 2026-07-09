<?php

namespace App\Filament\SuperAdmin\Resources\Evidences;

use App\Filament\SuperAdmin\Resources\Evidences\Pages\CreateEvidences;
use App\Filament\SuperAdmin\Resources\Evidences\Pages\EditEvidences;
use App\Filament\SuperAdmin\Resources\Evidences\Pages\ListEvidences;
use App\Filament\SuperAdmin\Resources\Evidences\Pages\ViewEvidences;
use App\Filament\SuperAdmin\Resources\Evidences\Schemas\EvidencesForm;
use App\Filament\SuperAdmin\Resources\Evidences\Schemas\EvidencesInfolist;
use App\Filament\SuperAdmin\Resources\Evidences\Tables\EvidencesTable;
use App\Models\Evidences;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EvidencesResource extends Resource
{
    protected static ?string $model = Evidences::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Evidences';

    public static function form(Schema $schema): Schema
    {
        return EvidencesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EvidencesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvidencesTable::configure($table);
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
            'index' => ListEvidences::route('/'),
            'create' => CreateEvidences::route('/create'),
            'view' => ViewEvidences::route('/{record}'),
            'edit' => EditEvidences::route('/{record}/edit'),
        ];
    }
}
