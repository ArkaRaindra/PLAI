<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Realization;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class PpeppAchievementChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Capaian Realisasi per Unit Organisasi';

    protected function getData(): array
    {
        $periodId = $this->filters['quality_period_id'] ?? null;

        $realizationsByUnit = Realization::query()
            ->where('status', 'approved')
            ->whereHas('target', fn($query) => $query->when(
                $periodId,
                fn($q) => $q->where('quality_period_id', $periodId),
            ))
            ->with(['target', 'organizationUnit'])
            ->get()
            ->filter(fn(Realization $realization) => $realization->target?->target_value > 0)
            ->groupBy('organization_unit_id');

        $labels = [];
        $values = [];

        foreach ($realizationsByUnit as $unitId => $realizations) {
            $labels[] = $realizations->first()->organizationUnit?->name ?? "Unit #{$unitId}";

            $average = $realizations
                ->map(fn(Realization $realization) => ((float) $realization->actual_value / (float) $realization->target->target_value) * 100)
                ->avg();

            $values[] = round($average, 1);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Capaian (%)',
                    'data' => $values,
                    'backgroundColor' => '#6366f1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
