<?php

namespace App\Filament\SuperAdmin\Resources\AuditAssignments\Pages;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAuditAssignment extends EditRecord
{
    protected static string $resource = AuditAssignmentResource::class;

    protected static ?string $title = 'Ubah Audit Assignment';

    public function getBreadcrumbs(): array
    {
        return [
            AuditAssignmentResource::getListUrl($this->record->audit_cycle_id) => 'Audit Assignment',
            'Ubah Audit Assignment',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditAssignmentResource::getListUrl($this->record->audit_cycle_id))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            ViewAction::make(),
            DeleteAction::make()
                ->successRedirectUrl(AuditAssignmentResource::getListUrl($this->record->audit_cycle_id)),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['audit_cycle_id'] = $this->record->audit_cycle_id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return AuditAssignmentResource::getListUrl($this->record->audit_cycle_id);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(AuditAssignmentResource::getListUrl($this->record->audit_cycle_id));
    }
}