<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings\Pages;

use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewAuditFinding extends ViewRecord
{
    protected static string $resource = AuditFindingResource::class;

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            AuditFindingResource::getListUrl($this->record->audit_assignment_id) => 'Audit Findings',
            'Detail Temuan Audit',
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
            EditAction::make(),
            AuditFindingResource::closeAction(),
        ];
    }
}