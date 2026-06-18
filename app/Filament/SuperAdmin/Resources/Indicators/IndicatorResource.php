<?php

namespace App\Filament\SuperAdmin\Resources\Indicators;

use App\Filament\SuperAdmin\Resources\Indicators\Pages\CreateIndicator;
use App\Filament\SuperAdmin\Resources\Indicators\Pages\EditIndicator;
use App\Filament\SuperAdmin\Resources\Indicators\Pages\ListIndicators;
use App\Filament\SuperAdmin\Resources\Indicators\Schemas\IndicatorForm;
use App\Filament\SuperAdmin\Resources\Indicators\Tables\IndicatorsTable;
use App\Models\Indicator;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IndicatorResource extends Resource
{
    protected static ?string $model = Indicator::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Data';

    protected static ?string $navigationLabel = 'Indikator';

    protected static ?string $pluralLabel = 'Indikator';

    protected static ?string $recordTitleAttribute = 'code';

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
