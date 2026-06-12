<?php

namespace App\Filament\Prodi\Resources\AuditEvidence\Pages;

use App\Filament\Prodi\Resources\AuditEvidence\AuditEvidenceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditAuditEvidence extends EditRecord
{
    protected static string $resource = AuditEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return AuditEvidenceResource::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->status === 'returned' && $data['status'] === 'draft') {

        }
        return $data;
    }
}
