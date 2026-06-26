<?php

namespace App\Filament\SuperAdmin\Resources\Positions;

use App\Filament\SuperAdmin\Resources\Positions\Pages\CreatePosition;
use App\Filament\SuperAdmin\Resources\Positions\Pages\EditPosition;
use App\Filament\SuperAdmin\Resources\Positions\Pages\ListPositions;
use App\Filament\SuperAdmin\Resources\Positions\Schemas\PositionForm;
use App\Filament\SuperAdmin\Resources\Positions\Tables\PositionsTable;
use App\Models\Position;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Masters';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Jabatan';

    protected static ?string $pluralModelLabel = 'Jabatan';

    public static function form(Schema $schema): Schema
    {
        return PositionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PositionsTable::configure($table);
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
            'index' => ListPositions::route('/'),
            'create' => CreatePosition::route('/create'),
            'edit' => EditPosition::route('/{record}/edit'),
        ];
    }
}
