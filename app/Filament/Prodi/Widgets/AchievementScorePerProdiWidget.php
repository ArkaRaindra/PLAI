<?php

namespace App\Filament\Prodi\Widgets;

use App\Models\AuditScore;
use App\Models\SubStandard;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class AchievementScorePerProdiWidget extends ChartWidget
{
    protected ?string $heading = 'Pencapaian Nilai per Program Studi';

    protected int|string|array $columnSpan = 6;

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $periodId = auth()->user()->period_id;
        $target = SubStandard::avg('max_score') ?? 0;

        $prodis = User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['prodi', 'unit-penunjang']))
            ->leftJoin('study_programs', 'users.study_program_id', '=', 'study_programs.id')
            ->where('users.is_active', true)
            ->orderBy('study_programs.name')
            ->orderBy('users.name')
            ->select('users.*')
            ->get();

        $prodiIds = $prodis->pluck('id');

        $averageScores = AuditScore::query()
            ->join('audit_evidences', 'audit_scores.audit_evidence_id', '=', 'audit_evidences.id')
            ->selectRaw('audit_evidences.user_id, AVG(audit_scores.score) as average_score')
            ->whereIn('audit_evidences.user_id', $prodiIds)
            ->where('audit_evidences.status', 'approved')
            ->when($periodId, fn ($query) => $query->where('audit_evidences.period_id', $periodId))
            ->groupBy('audit_evidences.user_id')
            ->pluck('average_score', 'audit_evidences.user_id');

        $labels = [];
        $achieved = [];

        foreach ($prodis as $prodi) {
            $labels[] = $prodi->studyProgramName ?: $prodi->name;
            $achieved[] = round($averageScores[$prodi->id] ?? 0, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Target',
                    'data' => array_fill(0, count($labels), round($target, 2)),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.45)',
                    'borderColor' => 'rgb(34, 197, 94)',
                ],
                [
                    'label' => 'Nilai Tercapai',
                    'data' => $achieved,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.65)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'suggestedMax' => SubStandard::avg('max_score') ?? 4,
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['prodi', 'unit-penunjang', 'fakultas', 'super-admin']);
    }
}
