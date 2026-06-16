<?php

namespace App\Filament\Prodi\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Override;

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
                ->whereNotNull('study_program')
                ->select('study_program', 'faculty')
                ->distinct()
                ->get(),
        ];
    }
}
