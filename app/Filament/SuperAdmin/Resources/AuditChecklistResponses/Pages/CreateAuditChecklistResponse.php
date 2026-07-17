<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Pages;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\AuditChecklistResponseResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateAuditChecklistResponse extends CreateRecord
{
    public ?string $auditAssignmentId = null;

    protected static string $resource = AuditChecklistResponseResource::class;

    protected static ?string $title = 'Isi Jawaban Checklist';

    protected static bool $canCreateAnother = true;

    public function mount(): void
    {
        if (blank($this->auditAssignmentId)) {
            $this->redirect(AuditAssignmentResource::getUrl('index'));

            return;
        }
        parent::mount();
    }

    public function getBreadcrums(): array
    {
        if (blank($this->auditAssignmentId)) {
            return parent::getBreadcrumbs();
        }

        return [
            AuditChecklistResponseResource::getListUrl($this->auditAssignmentId) => 'Audit Response',
            'Isi Jawaban Checklist',
        ];
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->auditAssignmentId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('kembali')
                ->url(AuditChecklistResponseResource::getListUrl($this->auditAssignmentId))
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

    protected function mutateformDataBeforeCreate(array $data): array
    {
        $data['audit_assignment_id'] = (int) $this->auditAssignmentId;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return AuditChecklistResponseResource::getListUrl($this->auditAssignmentId);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(AuditChecklistResponseResource::getlistUrl($this->auditAssignmentId));
    }
}
