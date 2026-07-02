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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IndicatorOwnerResource extends Resource
{
    protected static ?string $model = IndicatorOwner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'IndicatorOwner';

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
