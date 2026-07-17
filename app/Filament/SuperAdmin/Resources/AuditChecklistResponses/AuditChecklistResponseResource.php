<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistResponses;

use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Pages\CreateAuditChecklistResponse;
use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Pages\EditAuditChecklistResponse;
use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Pages\ListAuditChecklistResponses;
use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Pages\ViewAuditChecklistResponse;
use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Schemas\AuditChecklistResponseForm;
use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Schemas\AuditChecklistResponseInfolist;
use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Tables\AuditChecklistResponsesTable;
use App\Models\AuditChecklistResponse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AuditChecklistResponseResource extends Resource
{
    protected static ?string $model = AuditChecklistResponse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Audit Response';

    protected static ?string $pluralModelLabel = 'Audit Response';

    protected static ?string $slug = 'audit-checklist-responses';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return AuditChecklistResponseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditChecklistResponseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditChecklistResponsesTable::configure($table);
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
            'index' => ListAuditChecklistResponses::route('/'),
            'create' => CreateAuditChecklistResponse::route('/create'),
            'view' => ViewAuditChecklistResponse::route('/{record}'),
            'edit' => EditAuditChecklistResponse::route('/{record}/edit'),
        ];
    }

    public static function getListUrl(int|string $auditAssignmentId): string
    {
        return static::getUrl('index').'?'.http_build_query([
            'auditAssignmentId' => $auditAssignmentId,
        ]);
    }

    public static function getCreateUrl(int|string $auditAssignmentId): string
    {
        return static::geturl('create').'?'.http_build_query([
            'auditAssignmentId' => $auditAssignmentId,
        ]);
    }
}
