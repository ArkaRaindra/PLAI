<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Pages;

use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\AuditChecklistResponseResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAuditChecklistResponse extends EditRecord
{
    protected static string $resource = AuditChecklistResponseResource::class;

    protected static ?string $title = 'Ubah Jawaban Checklist';

    public function getbreadcrumbs(): array
    {
        return [
            AuditChecklistResponseResource::getListUrl($this->record->audit_assignment_id) => 'Ausit Response',
            'ubah Jawaban Checklist',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditChecklistResponseResource::getListUrl($this->record->audit_assignment_id))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            DeleteAction::make()
                ->successRedirectUrl(AuditChecklistResponseResource::getListUrl($this->record->audit_assignment_id)),
        ];
    }

    protected function mutateformdataBeforesave(array $data): array
    {
        $data['audit_assignment_id'] = $this->record->audit_assignment_id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return AuditChecklistResponseResource::getListUrl($this->record->audit_assignment_id);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(AuditChecklistResponseResource::getListUrl($this->record->audit_assignment_id));
    }
}
