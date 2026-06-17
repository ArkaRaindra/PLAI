<?php

namespace App\Filament\Prodi\Pages;

use App\Filament\Prodi\Widgets\AchievementScorePerProdiWidget;
use App\Filament\Prodi\Widgets\RadarScoreWidget;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Dashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Squares2x2;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.prodi.pages.dashboard';

    public function getViewData(): array
    {
        return [
            'programs' => User::query()
                ->join('study_programs', 'users.study_program_id', '=', 'study_programs.id')
                ->select('study_programs.name as study_program')
                ->distinct()
                ->orderBy('study_programs.name')
                ->get(),
        ];
    }

    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         AchievementScorePerProdiWidget::class,
    //         RadarScoreWidget::class,
    //     ];
    // }

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'md' => 1,
            'xl' => 2,
        ];
    }
}
