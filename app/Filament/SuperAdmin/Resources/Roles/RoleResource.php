<?php

namespace App\Filament\SuperAdmin\Resources\Roles;

use App\Filament\SuperAdmin\Resources\Roles\Pages\CreateRole;
use App\Filament\SuperAdmin\Resources\Roles\Pages\EditRole;
use App\Filament\SuperAdmin\Resources\Roles\Pages\ListRoles;
use App\Filament\SuperAdmin\Resources\Roles\Schemas\RoleForm;
use App\Filament\SuperAdmin\Resources\Roles\Tables\RolesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::UserShield;

    protected static ?string $recordTitleAttribute = 'Role';

    protected static ?string $modelLabel = 'Role';

    protected static ?string $pluralModelLabel = 'Role';

    protected static string|\UnitEnum|null $navigationGroup = 'Masters';

    protected static ?string $navigationLabel = 'Role';

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
