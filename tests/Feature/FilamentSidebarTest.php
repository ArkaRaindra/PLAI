<?php

namespace Tests\Feature;

use App\Filament\SuperAdmin\Pages\ManageStandards;
use App\Filament\SuperAdmin\Resources\Indicators\IndicatorResource;
use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use App\Filament\SuperAdmin\Resources\Standards\StandardResource;
use App\Filament\SuperAdmin\Resources\Targets\TargetResource;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentIcon;
use Filament\View\PanelsIconAlias;
use Openplain\FilamentShadcnTheme\Color;
use Tests\TestCase;

class FilamentSidebarTest extends TestCase
{
    public function test_all_panels_have_collapsible_sidebar_on_desktop(): void
    {
        foreach (Filament::getPanels() as $panel) {
            $this->assertTrue(
                $panel->isSidebarCollapsibleOnDesktop(),
                "Panel [{$panel->getId()}] should have collapsible sidebar.",
            );
        }
    }

    public function test_all_panels_use_shadcn_primary_color(): void
    {
        foreach (Filament::getPanels() as $panel) {
            $this->assertSame(
                Color::Yellow,
                $panel->getColors()['primary'],
                "Panel [{$panel->getId()}] should use the Shadcn yellow primary palette.",
            );
        }
    }

    public function test_sidebar_toggle_uses_hamburger_icon(): void
    {
        $this->assertSame('heroicon-s-bars-3', FilamentIcon::resolve(PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON));
        $this->assertSame('heroicon-s-bars-3', FilamentIcon::resolve(PanelsIconAlias::SIDEBAR_EXPAND_BUTTON));
    }

    public function test_target_navigation_stays_active_on_realization_routes(): void
    {
        $pattern = TargetResource::getNavigationItemActiveRoutePattern();

        $this->assertContains(TargetResource::getRouteBaseName().'.*', $pattern);
        $this->assertContains(RealizationResource::getRouteBaseName().'.*', $pattern);
    }

    public function test_manage_standards_navigation_stays_active_on_indicator_and_standard_routes(): void
    {
        $pattern = ManageStandards::getNavigationItemActiveRoutePattern();

        $this->assertContains(ManageStandards::getRouteName(), $pattern);
        $this->assertContains(IndicatorResource::getRouteBaseName().'.*', $pattern);
        $this->assertContains(StandardResource::getRouteBaseName().'.*', $pattern);
    }
}
