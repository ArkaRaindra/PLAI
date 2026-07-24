<?php

namespace App\Filament\SuperAdmin\Resources\AuditAssignments\Pages;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use App\Filament\SuperAdmin\Resources\AuditCycles\AuditCycleResource;
use App\Models\AuditCycle;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;

class CreateAuditAssignment extends CreateRecord
{
    #[Url]
    public ?string $auditCycleId = null;

    protected static string $resource = AuditAssignmentResource::class;

    protected static ?string $title = 'Tugaskan Auditor';

    protected static bool $canCreateAnother = true;

    public function mount(): void
    {
        if (blank($this->auditCycleId)) {
            $this->redirect(AuditCycleResource::getUrl('index'));

            return;
        }

        parent::mount();
    }

    public function getBreadcrumbs(): array
    {
        if (blank($this->auditCycleId)) {
            return parent::getBreadcrumbs();
        }

        $cycle = AuditCycle::query()->find($this->auditCycleId);

        if ($cycle === null) {
            return parent::getBreadcrumbs();
        }

        return [
            AuditCycleResource::getUrl('index') => AuditCycleResource::getNavigationLabel(),
            AuditAssignmentResource::getListUrl($cycle->id) => $cycle->displayTitle(),
            'Tugaskan Auditor',
        ];
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->auditCycleId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditAssignmentResource::getListUrl($this->auditCycleId))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'audit_cycle_id' => (int) $this->auditCycleId,
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['audit_cycle_id'] = (int) $this->auditCycleId;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return AuditAssignmentResource::getListUrl($this->auditCycleId);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(AuditAssignmentResource::getListUrl($this->auditCycleId));
    }
}
