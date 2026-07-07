<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriods;

use App\Filament\SuperAdmin\Resources\QualityPeriods\Pages\CreateQualityPeriod;
use App\Filament\SuperAdmin\Resources\QualityPeriods\Pages\EditQualityPeriod;
use App\Filament\SuperAdmin\Resources\QualityPeriods\Pages\ListQualityPeriods;
use App\Filament\SuperAdmin\Resources\QualityPeriods\Pages\ViewQualityPeriod;
use App\Filament\SuperAdmin\Resources\QualityPeriods\Schemas\QualityPeriodForm;
use App\Filament\SuperAdmin\Resources\QualityPeriods\Tables\QualityPeriodsTable;
use App\Models\QualityPeriod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;

class QualityPeriodResource extends Resource
{
    protected static ?string $model = QualityPeriod::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::CalendarEvent;

    protected static ?string $recordTitleAttribute = 'Periode';

    protected static ?string $modelLabel = 'Periode';

    protected static ?string $pluralModelLabel = 'Periode';

    protected static string|\UnitEnum|null $navigationGroup = 'Penetapan';

    protected static ?string $navigationLabel = 'Periode';

    public static function form(Schema $schema): Schema
    {
        return QualityPeriodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QualityPeriodsTable::configure($table);
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
            'index' => ListQualityPeriods::route('/'),
            'create' => CreateQualityPeriod::route('/create'),
            'view' => ViewQualityPeriod::route('/{record}'),
            'edit' => EditQualityPeriod::route('/{record}/edit'),
        ];
    }
}
