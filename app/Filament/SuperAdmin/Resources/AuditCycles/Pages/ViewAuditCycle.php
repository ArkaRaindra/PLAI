<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Pages;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use App\Filament\SuperAdmin\Resources\AuditCycles\AuditCycleResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewAuditCycle extends ViewRecord
{
    protected static string $resource = AuditCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
            Action::make('manageAssignments')
                ->label('Kelola Penugasan')
                ->icon(Heroicon::UserGroup)
                ->url(fn (): string => AuditAssignmentResource::getListUrl($this->record->id)),
            EditAction::make(),
            AuditCycleResource::activateAction(),
            AuditCycleResource::closeAction(),
        ];
    }
}
