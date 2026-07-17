<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Pages;

use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\AuditChecklistResponseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAuditChecklistResponse extends ViewRecord
{
    protected static string $resource = AuditChecklistResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
