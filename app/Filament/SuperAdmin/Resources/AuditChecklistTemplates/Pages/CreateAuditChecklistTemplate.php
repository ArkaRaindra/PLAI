<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\AuditChecklistTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuditChecklistTemplate extends CreateRecord
{
    protected static string $resource = AuditChecklistTemplateResource::class;

    protected function getRedirecturl(): string
    {
        return static::$resource::getUrl('index');
    }
}
