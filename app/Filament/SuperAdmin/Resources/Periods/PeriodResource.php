<?php

namespace App\Filament\SuperAdmin\Resources\Periods;

use App\Filament\SuperAdmin\Resources\Periods\Pages\CreatePeriod;
use App\Filament\SuperAdmin\Resources\Periods\Pages\EditPeriod;
use App\Filament\SuperAdmin\Resources\Periods\Pages\ListPeriods;
use App\Filament\SuperAdmin\Resources\Periods\Schemas\PeriodForm;
use App\Filament\SuperAdmin\Resources\Periods\Tables\PeriodsTable;
use App\Models\Period;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PeriodResource extends Resource
{
    protected static ?string $model = Period::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDateRange;

    protected static string|\UnitEnum|null $navigationGroup = 'Data';

    public static function form(Schema $schema): Schema
    {
        return PeriodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PeriodsTable::configure($table);
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
            'index' => ListPeriods::route('/'),
            'create' => CreatePeriod::route('/create'),
            'edit' => EditPeriod::route('/{record}/edit'),
        ];
    }
}
