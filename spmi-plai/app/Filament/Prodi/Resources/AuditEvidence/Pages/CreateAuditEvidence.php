<?php

namespace App\Filament\Prodi\Resources\AuditEvidence\Pages;

use App\Filament\Prodi\Resources\AuditEvidence\AuditEvidenceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuditEvidence extends CreateRecord
{
    protected static string $resource = AuditEvidenceResource::class;

     protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
}
