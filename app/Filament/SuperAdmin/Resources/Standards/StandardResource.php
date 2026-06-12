<?php

namespace App\Filament\SuperAdmin\Resources\Standards;

use App\Filament\SuperAdmin\Resources\Standards\Pages\CreateStandard;
use App\Filament\SuperAdmin\Resources\Standards\Pages\EditStandard;
use App\Filament\SuperAdmin\Resources\Standards\Pages\ListStandards;
use App\Filament\SuperAdmin\Resources\Standards\Schemas\StandardForm;
use App\Filament\SuperAdmin\Resources\Standards\Tables\StandardsTable;
use App\Models\Standard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StandardResource extends Resource
{
    protected static ?string $model = Standard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StandardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StandardsTable::configure($table);
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
            'index' => ListStandards::route('/'),
            'create' => CreateStandard::route('/create'),
            'edit' => EditStandard::route('/{record}/edit'),
        ];
    }
}
