<?php

namespace App\Filament\Prodi\Resources\AuditEvidence\Pages;

use App\Filament\Prodi\Resources\AuditEvidence\AuditEvidenceResource;
use App\Models\AuditScore;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $score = AuditScore::where('sub_standard_id', $record->sub_standard_id)
            ->where('user_id', $record->user_id)
            ->where('period_id', $record->period_id)
            ->first();
        $data['score'] = $score?->score;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->scoreValue = $data['score'] ?? null;
        unset($data['score']);

        return $data;
    }

    protected ?int $scoreValue = null;

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        if ($this->scoreValue !== null) {
            AuditScore::updateOrCreate(
                [
                    'sub_standard_id' => $record->sub_standard_id,
                    'user_id' => $record->user_id,
                    'period_id' => $record->period_id,
                ],
                [
                    'score' => $this->scoreValue,
                    'auditor_id' => auth()->id(),
                    'comment' => $record->auditor_note,
                ]
            );
        }
    }
}
