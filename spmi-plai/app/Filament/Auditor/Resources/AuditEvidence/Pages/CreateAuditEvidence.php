<?php

namespace App\Filament\Auditor\Resources\AuditEvidence\Pages;

use App\Filament\Auditor\Resources\AuditEvidence\AuditEvidenceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuditEvidence extends CreateRecord
{
    protected static string $resource = AuditEvidenceResource::class;
}
