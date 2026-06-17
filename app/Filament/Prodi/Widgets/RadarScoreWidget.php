<?php

namespace App\Filament\Prodi\Widgets;

use App\Models\AuditScore;
use App\Models\Standard;
use App\Models\SubStandard;
use Filament\Widgets\ChartWidget;

class RadarScoreWidget extends ChartWidget
{
    protected ?string $heading = 'Radar Target dan Nilai Tercapai per Standar';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 6;

    protected function getData(): array
    {
        $userId = auth()->id();
        $periodId = auth()->user()->period_id;
        $standards = Standard::with('subStandards')->orderBy('code')->get();

        $labels = [];
        $target = [];
        $achieved = [];

        foreach ($standards as $standard) {
            $subStandardIds = $standard->subStandards->pluck('id');
            if ($subStandardIds->isEmpty()) {
                continue;
            }

            $labels[] = $standard->code.' - '.$standard->name;
            $target[] = round(SubStandard::whereIn('id', $subStandardIds)->avg('max_score') ?? 0, 2);

            $subStandardScores = AuditScore::query()
                ->join('audit_evidences', 'audit_scores.audit_evidence_id', '=', 'audit_evidences.id')
                ->selectRaw('audit_evidences.sub_standard_id, AVG(audit_scores.score) as average_score')
                ->where('audit_evidences.user_id', $userId)
                ->where('audit_evidences.status', 'approved')
                ->when($periodId, fn ($query) => $query->where('audit_evidences.period_id', $periodId))
                ->whereIn('audit_evidences.sub_standard_id', $subStandardIds)
                ->groupBy('audit_evidences.sub_standard_id')
                ->pluck('average_score', 'audit_evidences.sub_standard_id');

            $achieved[] = round($subStandardScores->isEmpty() ? 0 : $subStandardScores->avg(), 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Target',
                    'data' => $target,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'pointBackgroundColor' => 'rgb(34, 197, 94)',
                    'pointBorderColor' => '#fff',
                ],
                [
                    'label' => 'Nilai Tercapai',
                    'data' => $achieved,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.25)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'pointBackgroundColor' => 'rgb(59, 130, 246)',
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

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'suggestedMax' => max(SubStandard::avg('max_score') ?? 4, 4),
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['prodi', 'unit-penunjang', 'fakultas', 'super-admin']);
    }
}
