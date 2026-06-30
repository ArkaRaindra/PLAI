<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriodes;

use App\Filament\SuperAdmin\Resources\QualityPeriodes\Pages\CreateQualityPeriode;
use App\Filament\SuperAdmin\Resources\QualityPeriodes\Pages\EditQualityPeriode;
use App\Filament\SuperAdmin\Resources\QualityPeriodes\Pages\ListQualityPeriodes;
use App\Filament\SuperAdmin\Resources\QualityPeriodes\Pages\ViewQualityPeriode;
use App\Filament\SuperAdmin\Resources\QualityPeriodes\Schemas\QualityPeriodeForm;
use App\Filament\SuperAdmin\Resources\QualityPeriodes\Schemas\QualityPeriodeInfolist;
use App\Filament\SuperAdmin\Resources\QualityPeriodes\Tables\QualityPeriodesTable;
use App\Models\QualityPeriode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QualityPeriodeResource extends Resource
{
    protected static ?string $model = QualityPeriode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'QualityPeriode';

    public static function form(Schema $schema): Schema
    {
        return QualityPeriodeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QualityPeriodeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QualityPeriodesTable::configure($table);
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
            'index' => ListQualityPeriodes::route('/'),
            'create' => CreateQualityPeriode::route('/create'),
            'view' => ViewQualityPeriode::route('/{record}'),
            'edit' => EditQualityPeriode::route('/{record}/edit'),
        ];
    }
}
