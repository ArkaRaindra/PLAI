<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\AuditChecklistTemplateResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

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
            Action::make('manageItems')
                ->label('Kelola Item')
                ->icon(Heroicon::ListBullet)
                ->url(fn (): string => AuditChecklistTemplateResource::getUrl('index')),
            EditAction::make(),
            AuditChecklistTemplateResource::activateAction(),
            AuditChecklistTemplateResource::newVersionAction(),
        ];
    }
}
