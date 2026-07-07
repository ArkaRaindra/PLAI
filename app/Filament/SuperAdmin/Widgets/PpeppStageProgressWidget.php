<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\SelfAssessment;
use App\Models\Target;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PpeppStageProgressWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $periodId = $this->filters['quality_period_id'] ?? null;

        $totalIndicators = max(Indicator::query()->count(), 1);
        $indicatorsWithTarget = Target::query()
            ->when($periodId, fn ($query) => $query->where('quality_period_id', $periodId))
            ->pluck('indicator_id')
            ->unique()
            ->count();
        $penetapanProgress = round(($indicatorsWithTarget / $totalIndicators) * 100, 1);

        $targetsQuery = Target::query()->when($periodId, fn ($query) => $query->where('quality_period_id', $periodId));
        $totalTargets = max((clone $targetsQuery)->count(), 1);
        $targetsWithRealization = (clone $targetsQuery)
            ->whereHas('realizations', fn ($query) => $query->whereIn('status', ['submitted', 'approved']))
            ->count();
        $pelaksanaanProgress = round(($targetsWithRealization / $totalTargets) * 100, 1);

        $totalUnits = max(OrganizationUnit::query()->where('is_active', true)->count(), 1);
        $unitsEvaluated = SelfAssessment::query()
            ->when($periodId, fn ($query) => $query->where('quality_period_id', $periodId))
            ->where('status', 'approved')
            ->pluck('organization_unit_id')
            ->unique()
            ->count();
        $evaluasiProgress = round(($unitsEvaluated / $totalUnits) * 100, 1);

        return [
            Stat::make('Penetapan', "{$penetapanProgress}%")
                ->description("{$indicatorsWithTarget} dari {$totalIndicators} indikator telah ditetapkan target")
                ->color($this->progressColor($penetapanProgress)),

            Stat::make('Pelaksanaan', "{$pelaksanaanProgress}%")
                ->description("{$targetsWithRealization} dari {$totalTargets} target telah direalisasikan dan disetujui")
                ->color($this->progressColor($pelaksanaanProgress)),

            Stat::make('Evaluasi', "{$evaluasiProgress}%")
                ->description("{$unitsEvaluated} dari {$totalUnits} unit organisasi telah menyelesaikan self assessment")
                ->color($this->progressColor($evaluasiProgress)),
        ];
    }

    private function progressColor(float $percentage): string
    {
        return match (true) {
            $percentage >= 80 => 'success',
            $percentage >= 40 => 'warning',
            default => 'danger',
        };
    }
}
