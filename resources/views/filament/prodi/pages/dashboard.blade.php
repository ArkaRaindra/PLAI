<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <x-program-widget-dashboard :programs="$programs" />

        <x-welcome-widget-dashboard :programs="$programs" />

        <x-footer-page :programs="$programs" />

    </div>
</x-filament-panels::page>