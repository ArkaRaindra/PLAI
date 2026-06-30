<?php

namespace App\Filament\SuperAdmin\Resources\OrgUnits;

use App\Filament\SuperAdmin\Resources\OrgUnits\Pages\CreateOrgUnit;
use App\Filament\SuperAdmin\Resources\OrgUnits\Pages\EditOrgUnit;
use App\Filament\SuperAdmin\Resources\OrgUnits\Pages\ListOrgUnits;
use App\Filament\SuperAdmin\Resources\OrgUnits\Pages\ViewOrgUnit;
use App\Filament\SuperAdmin\Resources\OrgUnits\Schemas\OrgUnitForm;
use App\Filament\SuperAdmin\Resources\OrgUnits\Schemas\OrgUnitInfolist;
use App\Filament\SuperAdmin\Resources\OrgUnits\Tables\OrgUnitsTable;
use App\Models\OrganizationUnit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;

class OrgUnitResource extends Resource
{
    protected static ?string $model = OrganizationUnit::class;

    // protected static string|BackedEnum|null $navigationIcon = Tabler::Affiliate;

    protected static ?string $recordTitleAttribute = 'Unit Organisasi';

    protected static ?string $modelLabel = 'Unit Organisasi';

    protected static ?string $pluralModelLabel = 'Unit Organisasi';

    protected static string|\UnitEnum|null $navigationGroup = 'Masters';

    protected static ?string $navigationLabel = 'Unit Organisasi';

    public static function form(Schema $schema): Schema
    {
        return OrgUnitForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrgUnitInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrgUnitsTable::configure($table);
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
            'index' => ListOrgUnits::route('/'),
            'create' => CreateOrgUnit::route('/create'),
            'view' => ViewOrgUnit::route('/{record}'),
            'edit' => EditOrgUnit::route('/{record}/edit'),
        ];
    }
}
