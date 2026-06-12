<?php

namespace App\Filament\Auditor\Resources\AuditEvidence\Pages;

use App\Filament\Auditor\Resources\AuditEvidence\AuditEvidenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAuditEvidence extends ListRecords
{
    protected static string $resource = AuditEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
