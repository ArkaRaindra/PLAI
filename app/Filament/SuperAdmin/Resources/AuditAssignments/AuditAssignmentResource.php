<?php

namespace App\Filament\SuperAdmin\Resources\AuditAssignments;

use App\Filament\SuperAdmin\Resources\AuditAssignments\Pages\CreateAuditAssignment;
use App\Filament\SuperAdmin\Resources\AuditAssignments\Pages\EditAuditAssignment;
use App\Filament\SuperAdmin\Resources\AuditAssignments\Pages\ListAuditAssignments;
use App\Filament\SuperAdmin\Resources\AuditAssignments\Pages\ViewAuditAssignment;
use App\Filament\SuperAdmin\Resources\AuditAssignments\Schemas\AuditAssignmentForm;
use App\Filament\SuperAdmin\Resources\AuditAssignments\Schemas\AuditAssignmentInfolist;
use App\Filament\SuperAdmin\Resources\AuditAssignments\Tables\AuditAssignmentsTable;
use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\AuditChecklistResponseResource;
use App\Models\AuditAssignment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AuditAssignmentResource extends Resource
{
    protected static ?string $model = AuditAssignment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Audit Assignment';

    protected static ?string $pluralModelLabel = 'Audit Assignment';

    protected static ?string $slug = 'audit-assignments';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationItemActiveRoutePattern(): string|array
    {
        return [
            static::getroutebaseName().'.*',
            AuditChecklistResponseResource::getRouteBaseName().'.*',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return AuditAssignmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditAssignmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditAssignmentsTable::configure($table);
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
            'index' => ListAuditAssignments::route('/'),
            'create' => CreateAuditAssignment::route('/create'),
            'view' => ViewAuditAssignment::route('/{record}'),
            'edit' => EditAuditAssignment::route('/{record}/edit'),
        ];
    }

    public static function getListUrl(int|string $auditCycleId): string
    {
        return static::getUrl('index').'?'.http_build_query([
            'auditCycleId' => $auditCycleId,
        ]);
    }

    public static function getCreateUrl(int|string $auditCycleId): string
    {
        return static::getUrl('create').'?'.http_build_query([
            'auditCycleId' => $auditCycleId,
        ]);
    }
}
