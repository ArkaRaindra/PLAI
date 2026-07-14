<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\OrganizationUnit;
use App\Models\Realization;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class PpeppAchievementChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Capaian Realisasi per Unit Organisasi';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $periodId = $this->filters['quality_period_id'] ?? null;

        $units = OrganizationUnit::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $realizationsByUnit = Realization::query()
            ->where('status', 'approved')
            ->whereHas('target', fn ($query) => $query->when(
                $periodId,
                fn ($q) => $q->where('quality_period_id', $periodId),
            ))
            ->with('target')
            ->get()
            ->filter(fn (Realization $realization) => (float) ($realization->target?->target_value ?? 0) > 0)
            ->groupBy('organization_unit_id');

        $labels = [];
        $values = [];

        foreach ($units as $unit) {
            $labels[] = $unit->name;

            $unitRealizations = $realizationsByUnit->get($unit->id);

            $values[] = $unitRealizations
                ? round(
                    $unitRealizations
                        ->map(fn (Realization $realization) => ((float) $realization->actual_value / (float) $realization->target->target_value) * 100)
                        ->avg(),
                    1,
                )
                : 0.0;
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
