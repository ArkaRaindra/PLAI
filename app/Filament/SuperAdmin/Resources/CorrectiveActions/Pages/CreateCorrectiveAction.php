<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages;

use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\CorrectiveActionResource;
use App\Models\AuditFinding;
use App\Models\CorrectiveAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;

class CreateCorrectiveAction extends CreateRecord
{
    #[Url]
    public ?string $auditFindingId = null;

    protected static string $resource = CorrectiveActionResource::class;

    protected static ?string $title = 'Buat Corrective Action';

    protected ?AuditFinding $auditFinding = null;

    public function mount(): void
    {
        if (blank($this->auditFindingId)) {
            $this->redirect(AuditFindingResource::getUrl('index'));

            return;
        }

        $this->auditFinding = AuditFinding::query()->with('auditAssignment')->find($this->auditFindingId);

        if ($this->auditFinding === null) {
            $this->redirect(AuditFindingResource::getUrl('index'));

            return;
        }

        $existing = CorrectiveAction::query()->where('audit_finding_id', $this->auditFindingId)->first();

        if ($existing !== null) {
            $this->redirect(CorrectiveActionResource::getUrl('view', ['record' => $existing->id]));

            return;
        }
        parent::mount();
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Buat Corrective Action',
        ];
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->auditFindingId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditFindingResource::getUrl('view', ['record' => $this->auditFindingId]))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'audit_finding_id' => (int) $this->auditFindingId,
            'organization_unit_id' => $this->auditFinding?->auditAssignment?->organization_unit_id,
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $auditFinding = AuditFinding::query()->with('auditAssignment')->find($this->auditFindingId);

        $data['audit_finding_id'] = (int) $this->auditFindingId;
        $data['organization_unit_id'] = $auditFinding?->auditAssignment?->organization_unit_id;
        $data['status'] = 'draft';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return CorrectiveActionResource::getUrl('view', ['record' => $this->getRecord()->id]);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(AuditFindingResource::getUrl('view', ['record' => $this->auditFindingId]));
    }
}
