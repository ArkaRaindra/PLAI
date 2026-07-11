<?php

namespace App\Filament\SuperAdmin\Resources\Evidences;

use App\Filament\SuperAdmin\Resources\EvidenceLinks\EvidenceLinkResource;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
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
            EvidenceReviewResource::getRouteBaseName().'.*',
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
            ->visible(fn (Evidences $record): bool => $record->workflowInstance?->isAt('draft') ?? false)
            ->authorize(fn (Evidences $record): bool => self::authorizeWorkflow('submit', $record))
            ->action(function (Evidences $record): void {
                $record->transitionWorkflowTo('submitted');

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
            ->visible(fn (Evidences $record): bool => $record->workflowInstance?->isAt('submitted') ?? false)
            ->authorize(fn (Evidences $record): bool => self::authorizeWorkflow('startReview', $record))
            ->action(function (Evidences $record): void {
                $record->transitionWorkflowTo('review');

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
            ->visible(fn (Evidences $record): bool => $record->workflowInstance?->isAt('review') ?? false)
            ->authorize(fn (Evidences $record): bool => self::authorizeWorkflow('approve', $record))
            ->action(function (Evidences $record, array $data): void {
                $record->transitionWorkflowTo('approved', $data['notes'] ?? null);

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
            ->visible(fn (Evidences $record): bool => $record->workflowInstance?->isAt('review') ?? false)
            ->authorize(fn (Evidences $record): bool => self::authorizeWorkflow('reject', $record))
            ->action(function (Evidences $record, array $data): void {
                $record->transitionWorkflowTo('rejected', $data['notes']);

                Notification::make()->title('Evidence ditolak')->danger()->send();
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
            ->visible(fn (Evidences $record): bool => $record->workflowInstance?->isAt('approved') ?? false)
            ->authorize(fn (Evidences $record): bool => self::authorizeWorkflow('publish', $record))
            ->action(function (Evidences $record): void {
                $record->transitionWorkflowTo('published');

                Notification::make()->title('Evidence dipublikasikan')->success()->send();
            });
    }

    private static function authorizeWorkflow(string $ability, Evidences $record): bool
    {
        $workflowInstance = $record->workflowInstance ?? WorkflowInstance::for($record);

        if ($workflowInstance === null) {
            return false;
        }

        return Auth::user()?->can($ability, $workflowInstance) ?? false;
    }
}