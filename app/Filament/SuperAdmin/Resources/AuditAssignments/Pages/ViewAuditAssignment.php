<?php

namespace App\Filament\SuperAdmin\Resources\AuditAssignments\Pages;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\AuditChecklistResponseResource;
use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use App\Models\AuditAssignment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewAuditAssignment extends ViewRecord
{
    protected static string $resource = AuditAssignmentResource::class;

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            AuditAssignmentResource::getListUrl($this->record->audit_cycle_id) => 'Audit Assignment',
            'Detail Audit Assignment',
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
            Action::make('fillChecklist')
                ->label('Isi Checklist')
                ->icon(Heroicon::ChatBubbleLeftRight)
                ->url(fn (): string => AuditChecklistResponseResource::getListUrl($this->record->id)),
             Action::make('manageFindings')
                    ->label('Kelola Temuan')
                    ->icon(Heroicon::ExclamationTriangle)
                    ->url(fn (AuditAssignment $record): string => AuditFindingResource::getListUrl($record->id)),
            EditAction::make(),
        ];
    }
}