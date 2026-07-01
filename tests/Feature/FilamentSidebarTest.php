<?php

namespace Tests\Feature;

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
}
