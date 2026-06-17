<?php

namespace App\Filament\Prodi\Resources\SubStandards;

use App\Filament\Prodi\Resources\SubStandards\Pages\CreateSubStandard;
use App\Filament\Prodi\Resources\SubStandards\Pages\EditSubStandard;
use App\Filament\Prodi\Resources\SubStandards\Pages\ListSubStandards;
use App\Filament\Prodi\Resources\SubStandards\Schemas\SubStandardForm;
use App\Filament\Prodi\Resources\SubStandards\Tables\SubStandardsTable;
use App\Models\SubStandard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubStandardResource extends Resource
{
    protected static ?string $model = SubStandard::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static string|\UnitEnum|null $navigationGroup = 'Sub Butir Kriteria';

    protected static ?string $pluralLabel = 'Sub Kriteria';

    protected static ?string $navigationLabel = 'Sub Butir Kriteria';

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
