<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Filament\Panel;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsIconAlias;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Openplain\FilamentShadcnTheme\Color;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configureFilamentPanels();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureFilamentIcons();
        $this->configureFilamentRenderHooks();
    }

    protected function configureFilamentIcons(): void
    {
        FilamentIcon::register([
            PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON => 'heroicon-s-bars-3',
            PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON_RTL => 'heroicon-s-bars-3',
            PanelsIconAlias::SIDEBAR_EXPAND_BUTTON => 'heroicon-s-bars-3',
            PanelsIconAlias::SIDEBAR_EXPAND_BUTTON_RTL => 'heroicon-s-bars-3',
        ]);
    }

    protected function configureFilamentPanels(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel
                ->sidebarCollapsibleOnDesktop()
                ->viteTheme('resources/css/filament/shared/base-theme.css')
                ->colors([
                    'primary' => Color::Yellow,
                ]);
        });
    }

    protected function configureFilamentRenderHooks(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn () => view('components.auth-back-to-home'),
        );
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
