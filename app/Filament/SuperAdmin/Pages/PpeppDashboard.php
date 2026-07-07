<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Filament\SuperAdmin\Widgets\PpeppAchievementChartWidget;
use App\Filament\SuperAdmin\Widgets\PpeppKpiOverviewWidget;
use App\Filament\SuperAdmin\Widgets\PpeppStageProgressWidget;
use App\Filament\SuperAdmin\Widgets\PpeppSummaryTableWidget;
use App\Models\QualityPeriod;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PpeppDashboard extends Dashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'ppepp-dashboard';

    protected static ?string $navigationLabel = 'Dashboard PPEPP';

    protected static ?string $title = 'Dashboard PPEPP';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?int $navigationSort = -1;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('quality_period_id')
                    ->label('Periode')
                    ->options(fn () => QualityPeriod::query()->pluck('name', 'id'))
                    ->placeholder('Semua Periode')
                    ->default(fn () => QualityPeriod::query()->where('is_active', true)->value('id'))
                    ->live(),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            PpeppKpiOverviewWidget::class,
            PpeppStageProgressWidget::class,
            PpeppAchievementChartWidget::class,
            PpeppSummaryTableWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
