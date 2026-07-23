<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings\Pages;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;

class CreateAuditFinding extends CreateRecord
{
    #[Url]
    public ?string $auditAssignmentId = null;

    protected static string $resource = AuditFindingResource::class;

    protected static ?string $title = 'Tambah Temuan Audit';

    protected static bool $canCreateAnother = true;

    public function mount(): void
    {
        if (blank($this->auditAssignmentId)) {
            $this->redirect(AuditAssignmentResource::getUrl('index'));

            return;
        }

        parent::mount();
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        if (blank($this->auditAssignmentId)) {
            return parent::getBreadcrumbs();
        }

        return [
            AuditFindingResource::getListUrl($this->auditAssignmentId) => 'Audit Findings',
            'Tambah Temuan Audit',
        ];
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->auditAssignmentId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditFindingResource::getListUrl($this->auditAssignmentId))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'audit_assignment_id' => (int) $this->auditAssignmentId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['audit_assignment_id'] = (int) $this->auditAssignmentId;
        $data['status'] = 'open';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return AuditFindingResource::getListUrl($this->auditAssignmentId);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(AuditFindingResource::getListUrl($this->auditAssignmentId));
    }
}