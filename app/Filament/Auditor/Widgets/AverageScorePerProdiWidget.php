<?php

namespace App\Filament\Auditor\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class AverageScorePerProdiWidget extends ChartWidget
{
    protected ?string $heading = 'Average Score Per Prodi Widget';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $prodis = User::role('prodi')->with(['scores', 'studyProgram'])->orderBy('studyProgram.name')->get();
        $labels = [];
        $data = [];

        foreach ($prodis as $prodi) {
            $avg = $prodi->scores->avg('score') ?? 0;
            $labels[] = $prodi->studyProgramName ?: $prodi->name;
            $data[] = round($avg, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Nilai Rata-rata',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole([
            'auditor',
            'fakultas',
            'super-admin',
        ]);
    }
}
