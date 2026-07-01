<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorMappings;

use App\Filament\SuperAdmin\Resources\IndicatorMappings\Pages\CreateIndicatorMapping;
use App\Filament\SuperAdmin\Resources\IndicatorMappings\Pages\EditIndicatorMapping;
use App\Filament\SuperAdmin\Resources\IndicatorMappings\Pages\ListIndicatorMappings;
use App\Filament\SuperAdmin\Resources\IndicatorMappings\Pages\ViewIndicatorMapping;
use App\Filament\SuperAdmin\Resources\IndicatorMappings\Schemas\IndicatorMappingForm;
use App\Filament\SuperAdmin\Resources\IndicatorMappings\Schemas\IndicatorMappingInfolist;
use App\Filament\SuperAdmin\Resources\IndicatorMappings\Tables\IndicatorMappingsTable;
use App\Models\IndicatorMapping;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;

class IndicatorMappingResource extends Resource
{
    protected static ?string $model = IndicatorMapping::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::Hierarchy3;

    protected static ?string $recordTitleAttribute = 'Pemetaan Indikator';

    protected static ?string $modelLabel = 'Pemetaan Indikator';

    protected static ?string $pluralModelLabel = 'Pemetaan Indikator';

    protected static string|\UnitEnum|null $navigationGroup = 'Indikator';

    protected static ?string $navigationLabel = 'Pemetaan Indikator';

    public static function form(Schema $schema): Schema
    {
        return IndicatorMappingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IndicatorMappingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IndicatorMappingsTable::configure($table);
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
            'index' => ListIndicatorMappings::route('/'),
            'create' => CreateIndicatorMapping::route('/create'),
            'view' => ViewIndicatorMapping::route('/{record}'),
            'edit' => EditIndicatorMapping::route('/{record}/edit'),
        ];
    }
}
