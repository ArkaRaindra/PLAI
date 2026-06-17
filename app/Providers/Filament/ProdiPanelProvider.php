<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Prodi\Pages\HomePage;
use App\Filament\Prodi\Resources\AuditEvidence\AuditEvidenceResource;
use App\Filament\Prodi\Widgets\RadarScoreWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ProdiPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('prodi')
            ->path('prodi')
            ->viteTheme('resources/css/filament/prodi/theme.css')
            ->brandName('Prodi Panel')
            ->resources([
                AuditEvidenceResource::class,
            ])
            ->globalSearch(false)
            ->login(Login::class)
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Prodi/Resources'), for: 'App\Filament\Prodi\Resources')
            ->discoverPages(in: app_path('Filament/Prodi/Pages'), for: 'App\Filament\Prodi\Pages')
            ->pages([
                // Dashboard::class,
                HomePage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Prodi/Widgets'), for: 'App\Filament\Prodi\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                RadarScoreWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Penilaian & Diagram')
                    ->collapsible(true)
                    ->icon(Heroicon::ChartPie),
                NavigationGroup::make()
                    ->label('Butir Kriteria')
                    ->collapsible(true)
                    ->icon(Heroicon::Folder),
                NavigationGroup::make()
                    ->label('Sub Butir Kriteria')
                    ->collapsible(true)
                     ->icon(Heroicon::QueueList),
                NavigationGroup::make()
                    ->label('Indikator Penilaian')
                    ->collapsible(true)
                     ->icon(Heroicon::ClipboardDocumentCheck),
                NavigationGroup::make()
                    ->label('Element & Berkas')
                    ->collapsible(true)
                     ->icon(Heroicon::DocumentText),
                NavigationGroup::make()
                    ->label('Pengaturan')
                    ->collapsible(true)
                    ->icon(Heroicon::Cog6Tooth),
            ])
            // ->topNavigation()
            // ->sidebarFullyCollapsibleOnDesktop(true)
            ->viteTheme('resources/css/filament/prodi/theme.css');
    }
}
