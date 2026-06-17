<?php

namespace App\Filament\Prodi\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class HomePage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    protected static ?string $navigationLabel = 'Home Page';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.prodi.pages.home-page';

    public function getViewData(): array
    {
        return [
            'programs' => User::query()
                ->join('study_programs', 'users.study_program_id', '=', 'study_programs.id')
                ->leftJoin('faculties', 'users.faculty_id', '=', 'faculties.id')
                ->select('study_programs.name as study_program', 'faculties.name as faculty')
                ->distinct()
                ->orderBy('study_programs.name')
                ->get(),
        ];
    }
}
