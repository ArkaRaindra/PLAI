<?php

namespace App\Filament\SuperAdmin\Resources\StandarVersions;

use App\Filament\SuperAdmin\Resources\StandarVersions\Pages\CreateStandarVersions;
use App\Filament\SuperAdmin\Resources\StandarVersions\Pages\EditStandarVersions;
use App\Filament\SuperAdmin\Resources\StandarVersions\Pages\ListStandarVersions;
use App\Filament\SuperAdmin\Resources\StandarVersions\Pages\ViewStandarVersions;
use App\Filament\SuperAdmin\Resources\StandarVersions\Schemas\StandarVersionsForm;
use App\Filament\SuperAdmin\Resources\StandarVersions\Schemas\StandarVersionsInfolist;
use App\Filament\SuperAdmin\Resources\StandarVersions\Tables\StandarVersionsTable;
use App\Models\StandardVersion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StandarVersionsResource extends Resource
{
    protected static ?string $model = StandardVersion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'StandardVersion';

    public static function form(Schema $schema): Schema
    {
        return StandarVersionsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StandarVersionsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StandarVersionsTable::configure($table);
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
            'index' => ListStandarVersions::route('/'),
            'create' => CreateStandarVersions::route('/create'),
            'view' => ViewStandarVersions::route('/{record}'),
            'edit' => EditStandarVersions::route('/{record}/edit'),
        ];
    }
}
