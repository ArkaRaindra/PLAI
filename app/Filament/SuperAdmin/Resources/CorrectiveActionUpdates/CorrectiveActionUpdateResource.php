<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates;

use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Pages\CreateCorrectiveActionUpdate;
use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Pages\EditCorrectiveActionUpdate;
use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Pages\ListCorrectiveActionUpdates;
use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Pages\ViewCorrectiveActionUpdate;
use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Schemas\CorrectiveActionUpdateForm;
use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Schemas\CorrectiveActionUpdateInfolist;
use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Tables\CorrectiveActionUpdatesTable;
use App\Models\CorrectiveActionUpdate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CorrectiveActionUpdateResource extends Resource
{
    protected static ?string $model = CorrectiveActionUpdate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Progress Update';

    protected static ?string $pluralModelLabel = 'progress Update';

    protected static ?string $slug = 'corrective-action-updates';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    // public static function form(Schema $schema): Schema
    // {
    //     return CorrectiveActionUpdateForm::configure($schema);
    // }

    // public static function infolist(Schema $schema): Schema
    // {
    //     return CorrectiveActionUpdateInfolist::configure($schema);
    // }

    // public static function table(Table $table): Table
    // {
    //     return CorrectiveActionUpdatesTable::configure($table);
    // }

    // public static function getRelations(): array
    // {
    //     return [
    //         //
    //     ];
    // }

    public static function getPages(): array
    {
        return [
            'index' => ListCorrectiveActionUpdates::route('/'),
            'create' => CreateCorrectiveActionUpdate::route('/create'),
            // 'view' => ViewCorrectiveActionUpdate::route('/{record}'),
            // 'edit' => EditCorrectiveActionUpdate::route('/{record}/edit'),
        ];
    }

    public static function getListUrl(int|string $correctiveActionId): string
    {
        return static::getUrl('index').'?'.http_build_query([
            'correctiveActionId' => $correctiveActionId,
        ]);
    }

    public static function getCreateUrl(int|string $correctiveActionId): string
    {
        return static::getUrl('create').'?'.http_build_query([
            'correctiveActionId' => $correctiveActionId,
        ]);
    }
}
