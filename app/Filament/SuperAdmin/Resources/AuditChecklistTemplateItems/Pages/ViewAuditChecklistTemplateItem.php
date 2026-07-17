<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Pages;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\AuditChecklistTemplateItemResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAuditChecklistTemplateItem extends ViewRecord
{
    protected static string $resource = AuditChecklistTemplateItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
