<?php

namespace App\Filament\Auditor\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class AverageScorePerProdiWidget extends ChartWidget
{
    protected ?string $heading = 'Rata-rata nilai per Prodi';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $prodis = User::query()
            ->role('prodi')
            ->leftJoin('study_programs', 'users.study_program_id', '=', 'study_programs.id')
            ->orderBy('study_programs.name')
            ->orderBy('users.name')
            ->with(['scores', 'studyProgram'])
            ->select('users.*')
            ->get();
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
