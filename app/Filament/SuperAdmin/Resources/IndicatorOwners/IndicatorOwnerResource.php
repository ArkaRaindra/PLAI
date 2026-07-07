<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorOwners;

use App\Filament\SuperAdmin\Resources\IndicatorOwners\Pages\CreateIndicatorOwner;
use App\Filament\SuperAdmin\Resources\IndicatorOwners\Pages\EditIndicatorOwner;
use App\Filament\SuperAdmin\Resources\IndicatorOwners\Pages\ListIndicatorOwners;
use App\Filament\SuperAdmin\Resources\IndicatorOwners\Pages\ViewIndicatorOwner;
use App\Filament\SuperAdmin\Resources\IndicatorOwners\Schemas\IndicatorOwnerForm;
use App\Filament\SuperAdmin\Resources\IndicatorOwners\Schemas\IndicatorOwnerInfolist;
use App\Filament\SuperAdmin\Resources\IndicatorOwners\Tables\IndicatorOwnersTable;
use App\Models\IndicatorOwner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;

class IndicatorOwnerResource extends Resource
{
    protected static ?string $model = IndicatorOwner::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::UserStar;

    protected static ?string $recordTitleAttribute = 'Pemilik Indikator';

    protected static ?string $modelLabel = 'Pemilik Indikator';

    protected static ?string $pluralModelLabel = 'Pemilik Indikator';

    protected static string|\UnitEnum|null $navigationGroup = 'Indikator';

    protected static ?string $navigationLabel = 'Pemilik Indikator';

    public static function form(Schema $schema): Schema
    {
        return IndicatorOwnerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IndicatorOwnerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IndicatorOwnersTable::configure($table);
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
            'index' => ListIndicatorOwners::route('/'),
            'create' => CreateIndicatorOwner::route('/create'),
            'view' => ViewIndicatorOwner::route('/{record}'),
            'edit' => EditIndicatorOwner::route('/{record}/edit'),
        ];
    }
}
