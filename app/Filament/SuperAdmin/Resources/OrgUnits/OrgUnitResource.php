<?php

namespace App\Filament\SuperAdmin\Resources\OrgUnits;

use App\Filament\SuperAdmin\Resources\OrgUnits\Pages\CreateOrgUnit;
use App\Filament\SuperAdmin\Resources\OrgUnits\Pages\EditOrgUnit;
use App\Filament\SuperAdmin\Resources\OrgUnits\Pages\ListOrgUnits;
use App\Filament\SuperAdmin\Resources\OrgUnits\Pages\ViewOrgUnit;
use App\Filament\SuperAdmin\Resources\OrgUnits\Schemas\OrgUnitForm;
use App\Filament\SuperAdmin\Resources\OrgUnits\Schemas\OrgUnitInfolist;
use App\Filament\SuperAdmin\Resources\OrgUnits\Tables\OrgUnitsTable;
use App\Models\OrgUnit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrgUnitResource extends Resource
{
    protected static ?string $model = OrgUnit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'OrganizationUnit';

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
