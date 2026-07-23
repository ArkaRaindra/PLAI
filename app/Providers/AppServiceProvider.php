<?php

namespace App\Providers;

use App\Models\AuditAssignment;
use App\Models\Evidences;
use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\Realization;
use App\Models\SelfAssessment;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\Target;
use App\Models\User;
use App\Models\WorkflowInstance;
use Carbon\CarbonImmutable;
use Filament\Panel;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsIconAlias;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
        $this->configureSuperAdminGate();
        Relation::enforceMorphMap([
            'indicator' => Indicator::class,
            'organization_unit' => OrganizationUnit::class,
            'realization' => Realization::class,
            'standard' => Standard::class,
            'standard_source' => StandardSource::class,
            'target' => Target::class,
            'self_assessment' => SelfAssessment::class,
            'user' => User::class,
            'evidence' => Evidences::class,
            'audit_assignment' => AuditAssignment::class,
        ]);
        $this->configureAuditorWorkflow();
    }

    protected function configureAuditorWorkflow(): void
    {
        WorkflowInstance::registerTransitions('audit_assignment', [
            'open' => ['assigned'],
            'assigned' => ['corrective_action'],
            'corrective_action' => ['verification'],
            'verification' => ['closed', 'corrective_action'],
            'closed' => [],
        ]);
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

    protected function configureSuperAdminGate(): void
    {
        Gate::before(function (User $user, string $ability, mixed $arguments): ?bool {
            if ($user->hasRole('super-admin')) {
                return true;
            }

            return null;
        });
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
