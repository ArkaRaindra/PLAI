<?php

namespace App\Filament\Prodi\Widgets;

use App\Models\AuditScore;
use App\Models\Standard;
use Filament\Widgets\ChartWidget;

class RadarScoreWidget extends ChartWidget
{
    protected ?string $heading = 'Capaian Nilai per Standar'; // Hapus static

    protected function getData(): array
    {
        $userId = auth()->id();
        $periodId = auth()->user()->period_id ?? null;

        $standards = Standard::with('subStandards')->get();
        $labels = [];
        $data = [];

        foreach ($standards as $standard) {
            $subStandardIds = $standard->subStandards->pluck('id');
            if ($subStandardIds->isEmpty()) continue;

            $avgScore = AuditScore::where('user_id', $userId)
                ->whereIn('sub_standard_id', $subStandardIds)
                ->when($periodId, fn($q) => $q->where('period_id', $periodId))
                ->avg('score');

            $labels[] = $standard->code . ' - ' . $standard->name;
            $data[] = round($avgScore ?? 0, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Nilai Rata-rata per Standar',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'pointBackgroundColor' => 'rgb(54, 162, 235)',
                    'pointBorderColor' => '#fff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }
}