<?php

namespace App\Filament\Prodi\Resources\AuditEvidence\Pages;

use App\Filament\Prodi\Resources\AuditEvidence\AuditEvidenceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateAuditEvidence extends CreateRecord
{
    protected static string $resource = AuditEvidenceResource::class;

    /**
     * @var array<int>
     */
    protected array $subStandardIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->subStandardIds = array_map('intval', $data['sub_standard_ids'] ?? []);

        $data['user_id'] = auth()->id();
        $data['status'] = 'draft';
        $data['sub_standard_id'] = $this->subStandardIds[0] ?? null;
        unset($data['sub_standard_ids']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->subStandards()->sync($this->subStandardIds);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->visible(false);
    }

    #[Override]
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan');
    }
}
