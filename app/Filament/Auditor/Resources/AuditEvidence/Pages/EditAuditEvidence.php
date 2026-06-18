<?php

namespace App\Filament\Auditor\Resources\AuditEvidence\Pages;

use App\Filament\Auditor\Resources\AuditEvidence\AuditEvidenceResource;
use App\Models\AuditScore;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditAuditEvidence extends EditRecord
{
    protected static string $resource = AuditEvidenceResource::class;

    #[Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $fallbackSubStandards = collect([$this->record->subStandard])->filter();
        $subStandards = $this->record->subStandards->isNotEmpty()
            ? $this->record->subStandards
            : $fallbackSubStandards;

        foreach ($subStandards as $subStandard) {
            AuditScore::updateOrCreate(
                [
                    'audit_evidence_id' => $this->record->id,
                    'sub_standard_id' => $subStandard->id,
                ],
                [
                    'user_id' => $this->record->user_id,
                    'period_id' => $this->record->period_id,
                    'score' => $data['score'],
                    'comment' => $data['auditor_note'],
                    'auditor_id' => auth()->id(),
                ]
            );
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->status = $this->data['status'];
        $this->record->save();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
