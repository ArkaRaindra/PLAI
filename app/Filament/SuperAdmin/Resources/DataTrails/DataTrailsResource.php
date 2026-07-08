<?php

namespace App\Filament\SuperAdmin\Resources\DataTrails;

use App\Filament\SuperAdmin\Resources\DataTrails\Pages\ListDataTrails;
use App\Filament\SuperAdmin\Resources\DataTrails\Pages\ViewDataTrails;
use App\Filament\SuperAdmin\Resources\DataTrails\Schemas\DataTrailsInfolist;
use App\Filament\SuperAdmin\Resources\DataTrails\Tables\DataTrailsTable;
use App\Models\TraceabilityLinks;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;

class DataTrailsResource extends Resource
{
    protected static ?string $model = TraceabilityLinks::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::Timeline;

    protected static ?string $navigationLabel = 'Jejak Data';

    protected static ?string $modelLabel = 'Jejak Data';

    protected static ?string $pluralModelLabel = 'Jejak Data';

    protected static ?string $slug = 'data-trails';

    protected static string|\UnitEnum|null $navigationGroup = 'Masters';

    public static function infolist(Schema $schema): Schema
    {
        return DataTrailsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DataTrailsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDataTrails::route('/'),
            'view' => ViewDataTrails::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
