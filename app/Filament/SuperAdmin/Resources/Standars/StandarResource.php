<?php

namespace App\Filament\SuperAdmin\Resources\Standars;

use App\Filament\SuperAdmin\Resources\Standars\Pages\CreateStandar;
use App\Filament\SuperAdmin\Resources\Standars\Pages\EditStandar;
use App\Filament\SuperAdmin\Resources\Standars\Pages\ListStandars;
use App\Filament\SuperAdmin\Resources\Standars\Pages\ViewStandar;
use App\Filament\SuperAdmin\Resources\Standars\Schemas\StandarForm;
use App\Filament\SuperAdmin\Resources\Standars\Schemas\StandarInfolist;
use App\Filament\SuperAdmin\Resources\Standars\Tables\StandarsTable;
use App\Models\Standar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StandarResource extends Resource
{
    protected static ?string $model = Standar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Standar';

    public static function form(Schema $schema): Schema
    {
        return StandarForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StandarInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StandarsTable::configure($table);
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
            'index' => ListStandars::route('/'),
            'create' => CreateStandar::route('/create'),
            'view' => ViewStandar::route('/{record}'),
            'edit' => EditStandar::route('/{record}/edit'),
        ];
    }
}
