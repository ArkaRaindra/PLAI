<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles;

use App\Filament\SuperAdmin\Resources\AuditCycles\Pages\CreateAuditCycle;
use App\Filament\SuperAdmin\Resources\AuditCycles\Pages\EditAuditCycle;
use App\Filament\SuperAdmin\Resources\AuditCycles\Pages\ListAuditCycles;
use App\Filament\SuperAdmin\Resources\AuditCycles\Pages\ViewAuditCycle;
use App\Filament\SuperAdmin\Resources\AuditCycles\RelationManagers\AssignmentsRelationManager;
use App\Filament\SuperAdmin\Resources\AuditCycles\Schemas\AuditCycleForm;
use App\Filament\SuperAdmin\Resources\AuditCycles\Schemas\AuditCycleInfolist;
use App\Filament\SuperAdmin\Resources\AuditCycles\Tables\AuditCyclesTable;
use App\Models\AuditCycle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;
use UnitEnum;

class AuditCycleResource extends Resource
{
    protected static ?string $model = AuditCycle::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::Refresh;

    protected static ?string $modelLabel = 'Siklus Audit';

    protected static ?string $pluralModelLabel = 'Siklus Audit';

    protected static string|UnitEnum|null $navigationGroup = 'AMI';

    protected static ?string $navigationLabel = 'Siklus Audit';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AuditCycleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditCycleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditCyclesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AssignmentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditCycles::route('/'),
            'create' => CreateAuditCycle::route('/create'),
            'view' => ViewAuditCycle::route('/{record}'),
            'edit' => EditAuditCycle::route('/{record}/edit'),
        ];
    }
}
