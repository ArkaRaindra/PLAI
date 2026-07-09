<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Indicator;
use App\Models\IndicatorOwner;
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

        $obligations = IndicatorOwner::query()
            ->with(['indicator.targets' => fn ($query) => $query
                ->when($periodId, fn ($q) => $q->where('quality_period_id', $periodId))
                ->with('realizations')])
            ->get()
            ->filter(fn (IndicatorOwner $owner) => $owner->indicator->targets->isNotEmpty());

        $totalExpected = $obligations->count();

        $pairs = $obligations->map(function (IndicatorOwner $owner) {
            $target = $owner->indicator->targets->first();

            return [
                'target' => $target,
                'realization' => $target->realizations->firstWhere('organization_unit_id', $owner->organization_unit_id),
            ];
        });

        $approvedPairs = $pairs->filter(fn (array $pair) => $pair['realization']?->status === 'approved');

        $achievements = $approvedPairs
            ->filter(fn (array $pair) => (float) $pair['target']->target_value > 0)
            ->map(fn (array $pair) => ((float) $pair['realization']->actual_value / (float) $pair['target']->target_value) * 100);

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

            Stat::make('Realisasi Disetujui', "{$approvedPairs->count()} / {$totalExpected}")
                ->description('Indikator unit yang sudah disetujui')
                ->icon('heroicon-o-check-circle')
                ->color($totalExpected > 0 && $approvedPairs->count() < $totalExpected ? 'warning' : 'success'),

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