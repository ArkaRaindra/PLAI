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
use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use App\Models\AuditAssignment;
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
            static::getRouteBaseName().'.*',
            AuditChecklistResponseResource::getRouteBaseName().'.*',
            AuditFindingResource::getRouteBaseName().'.*',
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

    public static function workflowStatusLabels(): array
    {
        return [
            'open' => 'Terbuka',
            'assigned' => 'Ditugaskan',
            'corrective_action' => 'Tindak Lanjut',
            'verification' => 'Verifikasi',
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
            ->visible(fn (AuditAssignment $record): bool => Auth::user()?->hasRole('super-admin') ?? false || self::workflowStatus($record) === 'open')
            ->authorize(fn (): bool => self::authorizeSuperAdmin())
            ->action(function (AuditAssignment $record): void {
                self::transitionForAction($record, 'assigned');

                Notification::make()->title('Penugasan auditor dikonfirmasi')->success()->send();
            });
    }

    public static function moveToCorrectiveActionAction(): Action
    {
        return Action::make('moveToCorrectiveAction')
            ->label('Ajukan Tindak Lanjut')
            ->icon(Heroicon::ExclamationTriangle)
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Ajukan Tindak Lanjut')
            ->modalDescription('Assignment akan masuk tahap tindak lanjut (corrective action) atas temuan audit.')
            ->modalSubmitActionLabel('Ya, Ajukan')
            ->schema([
                Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            ])
            ->visible(fn (AuditAssignment $record): bool => Auth::user()?->hasRole('super-admin') ?? false || self::workflowStatus($record) === 'assigned')
            ->authorize(fn (): bool => self::authorizeSuperAdmin())
            ->action(function (AuditAssignment $record, array $data): void {
                self::transitionForAction($record, 'corrective_action', $data['notes'] ?? null);

                Notification::make()->title('Assignment masuk tahap tindak lanjut')->success()->send();
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
            ->visible(fn (AuditAssignment $record): bool => Auth::user()?->hasRole('super-admin') ?? false || self::workflowStatus($record) === 'corrective_action')
            ->authorize(fn (): bool => self::authorizeSuperAdmin())
            ->action(function (AuditAssignment $record): void {
                self::transitionForAction($record, 'verification');

                Notification::make()->title('Assignment masuk tahap verifikasi')->success()->send();
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
            ->visible(fn (AuditAssignment $record): bool => Auth::user()?->hasRole('super-admin') ?? false || self::workflowStatus($record) === 'verification')
            ->authorize(fn (): bool => self::authorizeSuperAdmin())
            ->action(function (AuditAssignment $record, array $data): void {
                self::transitionForAction($record, 'corrective_action', $data['notes']);

                Notification::make()->title('Assignment dikembalikan ke tahap tindak lanjut')->warning()->send();
            });
    }

    public static function closeAssignmentAction(): Action
    {
        return Action::make('closeAssignment')
            ->label('Tutup Assignment')
            ->icon(Heroicon::CheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Tutup Audit Assignment')
            ->modalDescription('Tindak lanjut telah terverifikasi dan assignment akan ditutup.')
            ->modalSubmitActionLabel('Ya, Tutup')
            ->visible(fn (AuditAssignment $record): bool => Auth::user()?->hasRole('super-admin') ?? false || self::workflowStatus($record) === 'verification')
            ->authorize(fn (): bool => self::authorizeSuperAdmin())
            ->action(function (AuditAssignment $record): void {
                self::transitionForAction($record, 'closed');

                Notification::make()->title('Audit assignment ditutup')->success()->send();
            });
    }

    private static function workflowStatus(AuditAssignment $record): ?string
    {
        return $record->workflowInstance?->current_status;
    }

    private static function authorizeSuperAdmin(): bool
    {
        return Auth::user()?->hasRole('super-admin') ?? false;
    }

    private static function transitionForAction(AuditAssignment $record, string $status, ?string $notes = null): void
    {
        $workflowInstance = $record->workflowInstance ?? WorkflowInstance::initialize($record, 'open');

        if ($workflowInstance === null) {
            return;
        }

        if (Auth::user()?->hasRole('super-admin') ?? false) {
            $workflowInstance->forceTransitionTo($status, $notes);
        } else {
            $workflowInstance->transitionTo($status, $notes);
        }
    }
}
