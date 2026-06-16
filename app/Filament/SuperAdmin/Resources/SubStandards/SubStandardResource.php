<?php

namespace App\Filament\SuperAdmin\Resources\SubStandards;

use App\Filament\SuperAdmin\Resources\SubStandards\Pages\CreateSubStandard;
use App\Filament\SuperAdmin\Resources\SubStandards\Pages\EditSubStandard;
use App\Filament\SuperAdmin\Resources\SubStandards\Pages\ListSubStandards;
use App\Filament\SuperAdmin\Resources\SubStandards\Schemas\SubStandardForm;
use App\Filament\SuperAdmin\Resources\SubStandards\Tables\SubStandardsTable;
use App\Models\SubStandard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubStandardResource extends Resource
{
    protected static ?string $model = SubStandard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Masters';

    public static function form(Schema $schema): Schema
    {
        return SubStandardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubStandardsTable::configure($table);
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
            'index' => ListSubStandards::route('/'),
            'create' => CreateSubStandard::route('/create'),
            'edit' => EditSubStandard::route('/{record}/edit'),
        ];
    }
}
