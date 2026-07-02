<?php

namespace App\Filament\SuperAdmin\Resources\UserPositions;

use App\Filament\SuperAdmin\Resources\UserPositions\Pages\CreateUserPosition;
use App\Filament\SuperAdmin\Resources\UserPositions\Pages\EditUserPosition;
use App\Filament\SuperAdmin\Resources\UserPositions\Pages\ListUserPositions;
use App\Filament\SuperAdmin\Resources\UserPositions\Pages\ViewUserPosition;
use App\Filament\SuperAdmin\Resources\UserPositions\Schemas\UserPositionForm;
use App\Filament\SuperAdmin\Resources\UserPositions\Schemas\UserPositionInfolist;
use App\Filament\SuperAdmin\Resources\UserPositions\Tables\UserPositionsTable;
use App\Models\UserPosition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;

class UserPositionResource extends Resource
{
    protected static ?string $model = UserPosition::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::Briefcase;

    protected static ?string $recordTitleAttribute = 'Jabatan Pengguna';

    protected static ?string $modelLabel = 'Jabatan Pengguna';

    protected static ?string $pluralModelLabel = 'Jabatan Pengguna';

    protected static string|\UnitEnum|null $navigationGroup = 'Masters';

    protected static ?string $navigationLabel = 'Jabatan Pengguna';

    public static function form(Schema $schema): Schema
    {
        return UserPositionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserPositionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserPositionsTable::configure($table);
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
            'index' => ListUserPositions::route('/'),
            'create' => CreateUserPosition::route('/create'),
            'view' => ViewUserPosition::route('/{record}'),
            'edit' => EditUserPosition::route('/{record}/edit'),
        ];
    }
}
