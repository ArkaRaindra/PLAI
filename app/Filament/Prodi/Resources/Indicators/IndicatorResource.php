<?php

namespace App\Filament\Prodi\Resources\Indicators;

use App\Filament\Prodi\Resources\Indicators\Pages\CreateIndicator;
use App\Filament\Prodi\Resources\Indicators\Pages\EditIndicator;
use App\Filament\Prodi\Resources\Indicators\Pages\ListIndicators;
use App\Filament\Prodi\Resources\Indicators\Schemas\IndicatorForm;
use App\Filament\Prodi\Resources\Indicators\Tables\IndicatorsTable;
use App\Models\Indicator;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IndicatorResource extends Resource
{
    protected static ?string $model = Indicator::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static string|\UnitEnum|null $navigationGroup = 'Indikator Penilaian';

    protected static ?string $pluralLabel = 'Indikator Penilaian';

    protected static ?string $navigationLabel = 'Indikator Penilaian';

    public static function form(Schema $schema): Schema
    {
        return IndicatorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IndicatorsTable::configure($table);
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
            'index' => ListIndicators::route('/'),
            'create' => CreateIndicator::route('/create'),
            'edit' => EditIndicator::route('/{record}/edit'),
        ];
    }
}
