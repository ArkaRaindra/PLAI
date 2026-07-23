<?php

namespace App\Filament\SuperAdmin\Resources\Evidences;

use App\Filament\SuperAdmin\Resources\EvidenceLinks\EvidenceLinkResource;
use App\Filament\SuperAdmin\Resources\Evidences\Pages\CreateEvidences;
use App\Filament\SuperAdmin\Resources\Evidences\Pages\EditEvidences;
use App\Filament\SuperAdmin\Resources\Evidences\Pages\ListEvidences;
use App\Filament\SuperAdmin\Resources\Evidences\Pages\ViewEvidences;
use App\Filament\SuperAdmin\Resources\Evidences\Schemas\EvidencesForm;
use App\Filament\SuperAdmin\Resources\Evidences\Schemas\EvidencesInfolist;
use App\Filament\SuperAdmin\Resources\Evidences\Tables\EvidencesTable;
use App\Models\Evidences;
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

class EvidencesResource extends Resource
{
    protected static ?string $model = Evidences::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Evidences';

    public static function getNavigationItemActiveRoutePattern(): string|array
    {
        return [
            static::getRouteBaseName().'.*',
            EvidenceLinkResource::getRouteBaseName().'.*',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return EvidencesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EvidencesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvidencesTable::configure($table);
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
            'index' => ListEvidences::route('/'),
            'create' => CreateEvidences::route('/create'),
            'view' => ViewEvidences::route('/{record}'),
            'edit' => EditEvidences::route('/{record}/edit'),
        ];
    }

    /**
     * Human-readable labels for workflow_instances.current_status.
     *
     * @return array<string, string>
     */
    public static function workflowStatusLabels(): array
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'review' => 'Direview',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'published' => 'Dipublikasikan',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function workflowStatusColors(): array
    {
        return [
            'draft' => 'gray',
            'submitted' => 'info',
            'review' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'published' => 'primary',
        ];
    }

    /**
     * Evidence owner submits the draft for review.
     */
    public static function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Ajukan')
            ->icon(Heroicon::PaperAirplane)
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Ajukan Evidence')
            ->modalDescription('Evidence akan diajukan untuk masuk ke tahap review.')
            ->modalSubmitActionLabel('Ya, Ajukan')
            ->authorize(fn (Evidences $record): bool => self::authorizeSuperAdmin($record))
            ->action(function (Evidences $record): void {
                self::transitionForAction($record, 'submitted');

                Notification::make()->title('Evidence diajukan')->success()->send();
            });
    }

    /**
     * Admin Mutu/Auditor moves a submitted evidence into the review stage.
     */
    public static function startReviewAction(): Action
    {
        return Action::make('startReview')
            ->label('Mulai Review')
            ->icon(Heroicon::MagnifyingGlass)
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Mulai Review Evidence')
            ->modalDescription('Evidence akan masuk tahap review. Tugaskan reviewer melalui menu "Kelola Review".')
            ->modalSubmitActionLabel('Ya, Mulai Review')
            ->authorize(fn (Evidences $record): bool => self::authorizeSuperAdmin($record))
            ->action(function (Evidences $record): void {
                self::transitionForAction($record, 'review');

                Notification::make()->title('Evidence masuk tahap review')->success()->send();
            });
    }

    /**
     * Final approval of the evidence (after review).
     */
    public static function approveAction(): Action
    {
        return Action::make('approveWorkflow')
            ->label('Setujui')
            ->icon(Heroicon::CheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Setujui Evidence')
            ->modalSubmitActionLabel('Ya, Setujui')
            ->schema([
                Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            ])
            ->authorize(fn (Evidences $record): bool => self::authorizeSuperAdmin($record))
            ->action(function (Evidences $record, array $data): void {
                self::transitionForAction($record, 'approved', $data['notes'] ?? null);
                self::syncReviewNotes($record, $data['notes'] ?? null, 'approved');

                Notification::make()->title('Evidence disetujui')->success()->send();
            });
    }

    /**
     * Reject the evidence (sends it back to draft for revision).
     */
    public static function rejectAction(): Action
    {
        return Action::make('rejectWorkflow')
            ->label('Tolak')
            ->icon(Heroicon::XCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Tolak Evidence')
            ->modalDescription('Evidence akan dikembalikan ke status draft untuk direvisi.')
            ->modalSubmitActionLabel('Ya, Tolak')
            ->schema([
                Textarea::make('notes')->label('Alasan Penolakan')->required()->minLength(5)->columnSpanFull(),
            ])
            ->authorize(fn (Evidences $record): bool => self::authorizeSuperAdmin($record))
            ->action(function (Evidences $record, array $data): void {
                self::transitionForAction($record, 'rejected', $data['notes']);
                self::syncReviewNotes($record, $data['notes'], 'rejected');

                Notification::make()->title('Evidence ditolak')->danger()->send();
            });
    }

    /**
     * Send a rejected evidence back to draft so it can be revised and
     * re-submitted. Requires the "rejected -> draft" transition to be allowed.
     */
    public static function reviseAction(): Action
    {
        return Action::make('revise')
            ->label('Kirim Ulang')
            ->icon(Heroicon::ArrowUturnLeft)
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Kirim Ulang Evidence')
            ->modalDescription('Evidence akan dikembalikan ke status draft untuk direvisi sebelum diajukan kembali.')
            ->modalSubmitActionLabel('Ya, Kirim Ulang')
            ->authorize(fn (Evidences $record): bool => self::authorizeWorkflow('revise', $record))
            ->action(function (Evidences $record, array $data): void {
                self::transitionForAction($record, 'draft', $data['notes'] ?? null);
                self::syncReviewNotes($record, $data['notes'] ?? null, 'pending');

                Notification::make()->title('Evidence dikembalikan ke draft untuk direvisi')->success()->send();
            });
    }

    /**
     * Publish an approved evidence.
     */
    public static function publishAction(): Action
    {
        return Action::make('publish')
            ->label('Publikasikan')
            ->icon(Heroicon::GlobeAlt)
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Publikasikan Evidence')
            ->modalDescription('Evidence yang telah disetujui akan dipublikasikan dan tersedia bagi modul lain.')
            ->modalSubmitActionLabel('Ya, Publikasikan')
            ->authorize(fn (Evidences $record): bool => self::authorizeSuperAdmin($record))
            ->action(function (Evidences $record): void {
                self::transitionForAction($record, 'published');

                Notification::make()->title('Evidence dipublikasikan')->success()->send();
            });
    }

    private static function authorizeSuperAdmin(Evidences $record): bool
    {
        return Auth::user()?->hasRole('super-admin') ?? false;
    }

    private static function transitionForAction(Evidences $record, string $status, ?string $notes = null): void
    {
        if (Auth::user()?->hasRole('super-admin') ?? false) {
            $record->workflowInstance->forceTransitionTo($status, $notes);
        } else {
            $record->transitionWorkflowTo($status, $notes);
        }
    }

    private static function authorizeWorkflow(string $ability, Evidences $record): bool
    {
        $workflowInstance = $record->workflowInstance ?? WorkflowInstance::for($record);

        if ($workflowInstance === null) {
            return false;
        }

        return Auth::user()?->can($ability, $workflowInstance) ?? false;
    }

    /**
     * Persist the review notes (entered during approve/reject) onto the
     * linked EvidenceReview record so they show up in the review queue.
     */
    public static function syncReviewNotes(Evidences $record, ?string $notes, ?string $status = null): void
    {
        $review = $record->evidenceReviews()->firstOrNew(['evidence_id' => $record->id]);
        $review->review_notes = $notes;
        $review->reviewed_at ??= now();

        if ($status !== null) {
            $review->status = $status;
        }

        $review->save();
    }
}
