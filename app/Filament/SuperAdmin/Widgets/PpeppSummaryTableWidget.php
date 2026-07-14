<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\OrganizationUnit;
use App\Models\SelfAssessment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class PpeppSummaryTableWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $periodId = $this->filters['quality_period_id'] ?? null;

        return $table
            ->query(OrganizationUnit::query()->where('is_active', true))
            ->heading('Ringkasan Status PPEPP per Unit')
            ->columns([
                TextColumn::make('name')
                    ->label('Unit Organisasi')
                    ->searchable(),
                TextColumn::make('realizations_apperoved')
                    ->label('Realisasi Disetujui')
                    ->state(fn (OrganizationUnit $record): int => $record->realizations()
                        ->where('status', 'approved')
                        ->when($periodId, fn ($query) => $query->whereHas(
                            'target',
                            fn ($q) => $q->where('quality_period_id', $periodId),
                        ))
                        ->count()),
                TextColumn::make('self_assessments_status')
                    ->label('Status Self Assessment')
                    ->badge()
                    ->state(fn (OrganizationUnit $record): string => $this->selfAssessmentLabel($record, $periodId))
                    ->color(fn (OrganizationUnit $record): string => $this->selfAssessmentColor($record, $periodId)),

                TextColumn::make('final_score')
                    ->label('Final Score')
                    ->state(function (OrganizationUnit $record) use ($periodId): string {
                        $selfAssessment = $this->latestSelfAssessment($record, $periodId);

                        return $selfAssessment?->final_score !== null
                            ? number_format((float) $selfAssessment->final_score, 2)
                            : '-';
                    }),
            ])
            ->paginated([5, 10, 25]);
    }

    private function latestSelfAssessment(OrganizationUnit $record, mixed $periodId): ?SelfAssessment
    {
        return $record->selfAssessments()
            ->when($periodId, fn ($query) => $query->where('quality_period_id', $periodId))
            ->latest('created_at')
            ->first();
    }

    private function selfAssessmentLabel(OrganizationUnit $record, mixed $periodId): string
    {
        return match ($this->latestSelfAssessment($record, $periodId)?->status) {
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Belum Ada',
        };
    }

    private function selfAssessmentColor(OrganizationUnit $record, mixed $periodId): string
    {
        return match ($this->latestSelfAssessment($record, $periodId)?->status) {
            'submitted' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }
}
