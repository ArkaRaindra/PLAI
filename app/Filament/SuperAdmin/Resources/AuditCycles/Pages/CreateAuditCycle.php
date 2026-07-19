<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Pages;

use App\Filament\SuperAdmin\Resources\AuditCycles\AuditCycleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuditCycle extends CreateRecord
{
    protected static string $resource = AuditCycleResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
