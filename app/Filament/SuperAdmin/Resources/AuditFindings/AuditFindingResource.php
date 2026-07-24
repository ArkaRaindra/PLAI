<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings;

use App\Filament\SuperAdmin\Resources\AuditFindings\Pages\CreateAuditFinding;
use App\Filament\SuperAdmin\Resources\AuditFindings\Pages\EditAuditFinding;
use App\Filament\SuperAdmin\Resources\AuditFindings\Pages\ListAuditFindings;
use App\Filament\SuperAdmin\Resources\AuditFindings\Pages\ViewAuditFinding;
use App\Filament\SuperAdmin\Resources\AuditFindings\Schemas\AuditFindingForm;
use App\Filament\SuperAdmin\Resources\AuditFindings\Schemas\AuditFindingInfolist;
use App\Filament\SuperAdmin\Resources\AuditFindings\Tables\AuditFindingsTable;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\CorrectiveActionResource;
use App\Models\AuditFinding;
use App\Models\WorkflowInstance;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
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

    public static function manageCorrectiveActionAction(): Action
    {
        return Action::make('manageCorrectiveAction')
            ->label('Corrective Action')
            ->icon(Heroicon::ClipboardDocumentCheck)
            ->color('warning')
            ->url(fn (AuditFinding $record): string => CorrectiveActionResource::getListUrl($record->id));
    }

    public static function workflowStatusLabels(): array
    {
        return [
            'open' => 'Terbuka',
            'assigned' => 'Ditugaskan',
            'corrective_action' => 'Tindak Lanjut',
            'verification' => 'Diverifikasi',
            'closed' => 'Selesai',
        ];
    }

    public static function workflowStatusColors(): array
    {
        return [
            'open' => 'gray',
            'assigned' => 'info',
            'corrective_action' => 'warning',
            'verification' => 'primary',
            'closed' => 'success',
        ];
    }

    public static function assignAction(): Action
    {
        return Action::make('assignWorkflow')
            ->label('Konfirmasi Penugasan')
            ->icon(Heroicon::UserPlus)
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Penugasan Auditor')
            ->modalDescription('Auditor akan mulai melaksanakan audit untuk unit ini.')
            ->modalSubmitActionLabel('Ya, Konfirmasi')
            ->visible(fn (AuditFinding $record): bool => Auth::user()?->hasRole('super-admin') ?? false || self::workflowStatus($record) === 'open')
            ->authorize(fn (): bool => self::authorizeSuperAdmin())
            ->action(function (AuditFinding $record): void {
                self::transitionForAction($record, 'assigned');

                Notification::make()->title('Penugasan auditor dikonfirmasi')->success()->send();
            });
    }

    public static function moveToCorrectiveAction(): Action
    {
        return Action::make('moveToCorrectiveAction')
            ->label('Ajukan Tindak Lanjut')
            ->icon(Heroicon::ExclamationTriangle)
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Ajukan Tindak Lanjut')
            ->modalDescription('Temuan akan masuk tahap tindak lanjut (corrective action).')
            ->modalSubmitActionLabel('Ya, Ajukan')
            ->schema([
                Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            ])
            ->visible(fn (AuditFinding $record): bool => Auth::user()?->hasRole('super-admin') ?? false || self::workflowStatus($record) === 'assigned')
            ->authorize(fn (): bool => self::authorizeSuperAdmin())
            ->action(function (AuditFinding $record, array $data): void {
                self::transitionForAction($record, 'corrective_action', $data['notes'] ?? null);

                Notification::make()->title('Temuan masuk tahap tindak lanjut')->success()->send();
            });
    }

    public static function startVerificationAction(): Action
    {
        return Action::make('startVerification')
            ->label('Mulai Verifikasi')
            ->icon(Heroicon::MagnifyingGlass)
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Mulai Verifikasi Tindak Lanjut')
            ->modalDescription('Auditor akan memverifikasi tindak lanjut yang telah dilakukan auditee.')
            ->modalSubmitActionLabel('Ya, Mulai Verifikasi')
            ->visible(fn (AuditFinding $record): bool => Auth::user()?->hasRole('super-admin') ?? false || self::workflowStatus($record) === 'corrective_action')
            ->authorize(fn (): bool => self::authorizeSuperAdmin())
            ->action(function (AuditFinding $record): void {
                self::transitionForAction($record, 'verification');

                Notification::make()->title('Temuan masuk tahap verifikasi')->success()->send();
            });
    }

    public static function returnToCorrectiveActionAction(): Action
    {
        return Action::make('returnToCorrectiveAction')
            ->label('Kembalikan ke Tindak Lanjut')
            ->icon(Heroicon::ArrowUturnLeft)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Kembalikan ke Tindak Lanjut')
            ->modalDescription('Tindak lanjut dinilai belum memadai dan akan dikembalikan untuk diperbaiki.')
            ->modalSubmitActionLabel('Ya, Kembalikan')
            ->schema([
                Textarea::make('notes')->label('Alasan')->required()->minLength(5)->columnSpanFull(),
            ])
            ->visible(fn (AuditFinding $record): bool => Auth::user()?->hasRole('super-admin') ?? false || self::workflowStatus($record) === 'verification')
            ->authorize(fn (): bool => self::authorizeSuperAdmin())
            ->action(function (AuditFinding $record, array $data): void {
                self::transitionForAction($record, 'corrective_action', $data['notes']);

                Notification::make()->title('Temuan dikembalikan ke tahap tindak lanjut')->warning()->send();
            });
    }

    private static function workflowStatus(AuditFinding $record): ?string
    {
        return $record->workflowInstance?->current_status;
    }

    private static function authorizeSuperAdmin(): bool
    {
        return Auth::user()?->hasRole('super-admin') ?? false;
    }

    private static function transitionForAction(AuditFinding $record, string $status, ?string $notes = null): void
    {
        $workflowInstance = $record->workflowInstance ?? WorkflowInstance::initialize($record);

        if ($workflowInstance === null) {
            return;
        }

        if (Auth::user()?->hasRole('super-admin') ?? false) {
            $workflowInstance->forceTransitionTo($status, $notes);
        } else {
            $workflowInstance->transitionTo($status, $notes);
        }
    }

    public static function closeAction(): Action
    {
        return Action::make('closeFinding')
            ->label('Tutup Temuan')
            ->icon(Heroicon::CheckBadge)
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (AuditFinding $record): bool => $record->status === 'open' || Auth::user()?->hasRole('super-admin') ?? false || self::workflowStatus($record) === 'verification')
            ->action(function (AuditFinding $record): void {
                try {
                    $record->close();
                } catch (\RuntimeException $exception) {
                    Notification::make()->title($exception->getMessage())->danger()->send();

                    return;
                }

                $record->workflowInstance?->forceTransitionTo('closed');

                Notification::make()->title('Temuan ditutup')->success()->send();
            });
    }
}
