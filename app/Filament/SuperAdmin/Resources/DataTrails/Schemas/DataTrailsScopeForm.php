<?php

namespace App\Filament\SuperAdmin\Resources\DataTrails\Schemas;

use App\Models\SelfAssessment;
use Filament\Forms\Components\Select;

class DataTrailsScopeForm
{
    /**
     * @return array<int, Select>
     */
    public static function schema(): array
    {
        return [
            Select::make('selfAssessmentId')
                ->label('Penilaian Mandiri')
                ->placeholder('— Pilih Penilaian Mandiri —')
                ->options(fn (): array => SelfAssessment::query()
                    ->with(['organizationUnit', 'qualityPeriod'])
                    ->latest('created_at')
                    ->get()
                    ->mapWithKeys(fn (SelfAssessment $selfAssessment): array => [
                        $selfAssessment->id => self::optionLabel($selfAssessment),
                    ])
                    ->all())
                ->searchable()
                ->required()
                ->live(debounce: 300)
                ->columnSpanFull(),
        ];
    }

    public static function optionLabel(SelfAssessment $selfAssessment): string
    {
        $selfAssessment->loadMissing(['organizationUnit', 'qualityPeriod']);

        $unit = $selfAssessment->organizationUnit?->name ?? 'Unit tidak diketahui';
        $period = $selfAssessment->qualityPeriod?->name ?? 'Periode tidak diketahui';
        $status = match ($selfAssessment->status) {
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => $selfAssessment->status,
        };

        return "{$unit} — {$period} ({$status})";
    }

    /**
     * @return array<string, mixed>
     */
    public static function filterPayload(?string $selfAssessmentId): array
    {
        return [
            'self_assessment_id' => $selfAssessmentId,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public static function isComplete(array $filters): bool
    {
        return filled($filters['self_assessment_id'] ?? null);
    }
}
