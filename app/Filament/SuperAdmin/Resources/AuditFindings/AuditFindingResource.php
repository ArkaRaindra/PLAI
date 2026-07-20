<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings;

use App\Filament\SuperAdmin\Resources\AuditFindings\Pages\CreateAuditFinding;
use App\Filament\SuperAdmin\Resources\AuditFindings\Pages\EditAuditFinding;
use App\Filament\SuperAdmin\Resources\AuditFindings\Pages\ListAuditFindings;
use App\Filament\SuperAdmin\Resources\AuditFindings\Pages\ViewAuditFinding;
use App\Filament\SuperAdmin\Resources\AuditFindings\Schemas\AuditFindingForm;
use App\Filament\SuperAdmin\Resources\AuditFindings\Schemas\AuditFindingInfolist;
use App\Filament\SuperAdmin\Resources\AuditFindings\Tables\AuditFindingsTable;
use App\Models\AuditFinding;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AuditFindingResource extends Resource
{
    protected static ?string $model = AuditFinding::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Audit Finding';

    protected static ?string $pluralModelLabel = 'Audit Findings';

    protected static ?string $slug = 'audit-findings';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return AuditFindingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditFindingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditFindingsTable::configure($table);
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
            'index' => ListAuditFindings::route('/'),
            'create' => CreateAuditFinding::route('/create'),
            'view' => ViewAuditFinding::route('/{record}'),
            'edit' => EditAuditFinding::route('/{record}/edit'),
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
        return static::getUrl('create').'?'.http_build_query([
            'auditAssignmentId' => $auditAssignmentId,
        ]);
    }

    public static function closeAction(): Action
    {
        return Action::make('closeFinding')
            ->label('Tutup Temuan')
            ->icon(Heroicon::CheckBadge)
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (AuditFinding $record): bool => $record->status === 'open' || Auth::user()?->hasRole('super-admin') ?? false)
            ->action(function (AuditFinding $record): void {
                $record->close();

                Notification::make()->title('Temuan ditutup')->success()->send();
            });
    }
}
