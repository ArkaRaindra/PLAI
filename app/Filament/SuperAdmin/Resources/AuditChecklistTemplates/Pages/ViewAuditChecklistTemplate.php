<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\AuditChecklistTemplateResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAuditChecklistTemplate extends ViewRecord
{
    protected static string $resource = AuditChecklistTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
            EditAction::make(),
        ];
    }
}
