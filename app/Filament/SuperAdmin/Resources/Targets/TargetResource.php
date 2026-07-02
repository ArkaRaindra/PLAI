<?php

namespace App\Filament\SuperAdmin\Resources\Targets;

use App\Filament\SuperAdmin\Resources\Targets\Pages\CreateTarget;
use App\Filament\SuperAdmin\Resources\Targets\Pages\EditTarget;
use App\Filament\SuperAdmin\Resources\Targets\Pages\ListTargets;
use App\Filament\SuperAdmin\Resources\Targets\Pages\ViewTarget;
use App\Filament\SuperAdmin\Resources\Targets\Schemas\TargetForm;
use App\Filament\SuperAdmin\Resources\Targets\Schemas\TargetInfolist;
use App\Filament\SuperAdmin\Resources\Targets\Tables\TargetsTable;
use App\Models\Target;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;

class TargetResource extends Resource
{
    protected static ?string $model = Target::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::Target;

    protected static ?string $recordTitleAttribute = 'Target';

    protected static ?string $modelLabel = 'Target';

    protected static ?string $pluralModelLabel = 'Target';

    protected static string|\UnitEnum|null $navigationGroup = 'Indikator';

    protected static ?string $navigationLabel = 'Target';

    public static function form(Schema $schema): Schema
    {
        return TargetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TargetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TargetsTable::configure($table);
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
            'index' => ListTargets::route('/'),
            'create' => CreateTarget::route('/create'),
            'view' => ViewTarget::route('/{record}'),
            'edit' => EditTarget::route('/{record}/edit'),
        ];
    }
}
