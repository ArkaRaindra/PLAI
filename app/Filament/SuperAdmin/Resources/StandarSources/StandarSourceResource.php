<?php

namespace App\Filament\SuperAdmin\Resources\StandarSources;

use App\Filament\SuperAdmin\Resources\StandarSources\Pages\CreateStandarSource;
use App\Filament\SuperAdmin\Resources\StandarSources\Pages\EditStandarSource;
use App\Filament\SuperAdmin\Resources\StandarSources\Pages\ListStandarSources;
use App\Filament\SuperAdmin\Resources\StandarSources\Pages\ViewStandarSource;
use App\Filament\SuperAdmin\Resources\StandarSources\Schemas\StandarSourceForm;
use App\Filament\SuperAdmin\Resources\StandarSources\Schemas\StandarSourceInfolist;
use App\Filament\SuperAdmin\Resources\StandarSources\Tables\StandarSourcesTable;
use App\Models\StandardSource;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;

class StandarSourceResource extends Resource
{
    protected static ?string $model = StandardSource::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::Link;

    protected static ?string $recordTitleAttribute = 'Sumber Standar';

    protected static ?string $modelLabel = 'Sumber Standar';

    protected static ?string $pluralModelLabel = 'Sumber Standar';

    protected static string|\UnitEnum|null $navigationGroup = 'Penetapan';

    protected static ?string $navigationLabel = 'Sumber Standar';

    public static function form(Schema $schema): Schema
    {
        return StandarSourceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StandarSourceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StandarSourcesTable::configure($table);
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
            'index' => ListStandarSources::route('/'),
            'create' => CreateStandarSource::route('/create'),
            'view' => ViewStandarSource::route('/{record}'),
            'edit' => EditStandarSource::route('/{record}/edit'),
        ];
    }
}
