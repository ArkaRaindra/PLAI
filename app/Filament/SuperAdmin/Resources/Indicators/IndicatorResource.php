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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Indikator';

    protected static ?string $pluralModelLabel = 'Indikator';

    protected static ?string $slug = 'indicators';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return IndicatorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IndicatorsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIndicators::route('/'),
            'create' => CreateIndicator::route('/create'),
            'edit' => EditIndicator::route('/{record}/edit'),
        ];
    }

    public static function getListUrl(int|string $standardId): string
    {
        return static::getUrl('index').'?'.http_build_query([
            'standardId' => $standardId,
        ]);
    }

    public static function getCreateUrl(int|string $standardId): string
    {
        return static::getUrl('create').'?'.http_build_query([
            'standardId' => $standardId,
        ]);
    }
}
