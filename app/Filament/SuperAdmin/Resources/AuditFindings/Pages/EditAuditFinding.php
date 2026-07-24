<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings\Pages;

use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAuditFinding extends EditRecord
{
    protected static string $resource = AuditFindingResource::class;

    protected static ?string $title = 'Ubah Temuan Audit';

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            AuditFindingResource::getListUrl($this->record->audit_assignment_id) => 'Audit Findings',
            'Ubah Temuan Audit',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditFindingResource::getListUrl($this->record->audit_assignment_id))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            ViewAction::make(),
            DeleteAction::make()
                ->successRedirectUrl(AuditFindingResource::getListUrl($this->record->audit_assignment_id)),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['audit_assignment_id'] = $this->record->audit_assignment_id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return AuditFindingResource::getListUrl($this->record->audit_assignment_id);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(AuditFindingResource::getListUrl($this->record->audit_assignment_id));
    }
}
