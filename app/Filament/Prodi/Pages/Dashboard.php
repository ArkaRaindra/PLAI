<?php

namespace App\Filament\Prodi\Pages;

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
                ->whereNotNull('study_program')
                ->select('study_program')
                ->distinct()
                ->get(),
        ];
    }
}
