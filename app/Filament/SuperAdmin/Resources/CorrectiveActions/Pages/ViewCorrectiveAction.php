<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages;

use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\CorrectiveActionResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewCorrectiveAction extends ViewRecord
{
    protected static string $resource = CorrectiveActionResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            'Detail Corrective Action',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
           Action::make('back')
                ->label('Kembali')
                ->url(AuditFindingResource::getUrl('view', ['record' => $this->record->audit_finding_id]))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            EditAction::make()
                ->visible(fn (): bool => $this->record->isEditable()),
            CorrectiveActionResource::submitAction(),
        ];
    }
}
