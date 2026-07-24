<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Pages;

use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\CorrectiveActionUpdateResource;
use App\Models\CorrectiveAction;
use App\Services\Evidence\EvidenceService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateCorrectiveActionUpdate extends CreateRecord
{
    public ?string $correctiveActionId = null;

    protected static string $resource = CorrectiveActionUpdateResource::class;

    protected static ?string $title = 'Tambah Progress';

    protected ?array $pendingEvidence = null;

    public function mount(): void
    {
        if (blank($this->correctiveactionId)) {
            $this->redirect(CorrectiveActionUpdateResource::getUrl('index'));

            return;
        }

        $this->correctiveAction = CorrectiveAction::query()->find($this->correctiveActionId);

        if ($this->correctiveAction === null) {
            $this->redirect(CorrectiveActionUpdateResource::getUrl('index'));

            return;
        }

        if ($this->correctiveAction->status !== 'submitted') {
            Notification::make()
                ->title('Progress hanya dapat ditambahkan setelah Corrective Action disubmit.')
                ->warning()
                ->send();

            $this->redirect(CorrectiveActionUpdateResource::getListUrl($this->correctiveActionId));

            return;
        }
        parent::mount();
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Tambah Progress',
        ];
    }

    protected function getheaderActions(): array
    {
        if (blank($this->correctiveActionId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(CorrectiveActionUpdateResource::getListUrl($this->correctiveActionId))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'corrective_action_id' => (int) $this->correctiveActionId,
            'evidence_type' => 'none',
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $evidenceType = $data['evidence_type'] ?? 'none';

        $this->pendingEvidence = $evidenceType !== 'none'
            ? [
                'type' => $evidenceType,
                'file_path' => $data['evidence_file_path'] ?? null,
                'url_path' => $data['evidence_url_path'] ?? null,
            ]
            : null;

        unset($data['evidence_type'], $data['evidence_file_path'], $data['evidence_url_path']);

        $data['corrective_action_id'] = (int) $this->correctiveActionId;

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (\RuntimeException $exception) {
            Notification::make()->title($exception->getMessage())->danger()->send();

            throw new Halt();
        }
    }

    protected function afterCreate(): void
    {
        if ($this->pendingEvidence === null) {
            return;
        }

        $update = $this->getRecord();

        app(EvidenceService::class)->record(
            reference: $update,
             organizationUnitId: $this->correctiveAction->organization_unit_id,
            title: "Bukti Progress {$update->progress_percentage}% — {$update->updated_at->format('d/m/Y')}",
            description: null,
            type: $this->pendingEvidence['type'],
            filePath: $this->pendingEvidence['file_path'],
            urlPath: $this->pendingEvidence['url_path'],
            uploadedBy: Auth::id(),
        );
    }

    protected function getRedirectUrl(): string
    {
        return CorrectiveActionUpdateResource::getListUrl($this->correctiveActionId);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(CorrectiveActionUpdateResource::getListUrl($this->correctiveActionId));
    }
}
