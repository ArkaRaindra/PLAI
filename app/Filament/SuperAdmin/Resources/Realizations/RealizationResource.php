<?php

namespace App\Filament\SuperAdmin\Resources\Realizations;

use App\Filament\SuperAdmin\Resources\Realizations\Pages\CreateRealization;
use App\Filament\SuperAdmin\Resources\Realizations\Pages\EditRealization;
use App\Filament\SuperAdmin\Resources\Realizations\Pages\ListRealizations;
use App\Filament\SuperAdmin\Resources\Realizations\Pages\ViewRealization;
use App\Filament\SuperAdmin\Resources\Realizations\Schemas\RealizationForm;
use App\Filament\SuperAdmin\Resources\Realizations\Schemas\RealizationInfolist;
use App\Filament\SuperAdmin\Resources\Realizations\Tables\RealizationsTable;
use App\Models\Realization;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RealizationResource extends Resource
{
    protected static ?string $model = Realization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Realisasi';

    protected static ?string $pluralModelLabel = 'Realisasi';

    protected static ?string $slug = 'realizations';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return RealizationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RealizationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RealizationsTable::configure($table);
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
            'index' => ListRealizations::route('/'),
            'create' => CreateRealization::route('/create'),
            'view' => ViewRealization::route('/{record}'),
            'edit' => EditRealization::route('/{record}/edit'),
        ];
    }

    public static function getListUrl(int|string $targetId): string
    {
        return static::getUrl('index').'?'.http_build_query([
            'targetId' => $targetId,
        ]);
    }

    public static function getCreateUrl(int|string $targetId): string
    {
        return static::getUrl('create').'?'.http_build_query([
            'targetId' => $targetId,
        ]);
    }
}
