<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Indicator;
use App\Models\Realization;
use App\Models\Target;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PpeppKpiOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $periodId = $this->filters['quality_period_id'] ?? null;

        $totalIndicators = Indicator::query()->count();

        $totalTargets = Target::query()
            ->when($periodId, fn ($query) => $query->where('quality_period_id', $periodId))
            ->count();

        $realizationsInScope = Realization::query()
            ->whereHas('target', fn ($query) => $query->when(
                $periodId,
                fn ($q) => $q->where('quality_period_id', $periodId),
            ));

        $totalRealizations = (clone $realizationsInScope)->count();

        $approvedRealizations = (clone $realizationsInScope)
            ->where('status', 'approved')
            ->with('target')
            ->get();

        $achievements = $approvedRealizations
            ->filter(fn (Realization $realization) => $realization->target?->target_value > 0)
            ->map(fn (Realization $realization) => ((float) $realization->actual_value / (float) $realization->target->target_value) * 100);

        $averageAchievement = $achievements->isNotEmpty() ? $achievements->avg() : null;

        return [
            Stat::make('Total Indikator', (string) $totalIndicators)
                ->description('Indikator mutu terdaftar')
                ->icon('heroicon-o-chart-bar-square')
                ->color('gray'),

            Stat::make('Target Ditetapkan', (string) $totalTargets)
                ->description('Target pada periode terpilih')
                ->icon('heroicon-o-flag')
                ->color('info'),

            Stat::make('Realisasi Disetujui', "{$approvedRealizations->count()} / {$totalRealizations}")
                ->description('Realisasi yang disetujui dari seluruh realisasi periode ini')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Rata-rata Capaian', $averageAchievement !== null ? number_format($averageAchievement, 1).'%' : '-')
                ->description('Rata-rata realisasi disetujui terhadap target')
                ->icon('heroicon-o-presentation-chart-line')
                ->color(match (true) {
                    $averageAchievement === null => 'gray',
                    $averageAchievement >= 100 => 'success',
                    $averageAchievement >= 75 => 'warning',
                    default => 'danger',
                }),
        ];
    }
}
