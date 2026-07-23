<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\AuditChecklistTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListAuditChecklistTemplates extends ListRecords
{
    protected static string $resource = AuditChecklistTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::Plus),
        ];
    }
}
