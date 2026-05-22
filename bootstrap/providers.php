<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AuditorPanelProvider;
use App\Providers\Filament\FakultasPanelProvider;
use App\Providers\Filament\ProdiPanelProvider;
use App\Providers\Filament\SuperAdminPanelProvider;

return [
    AppServiceProvider::class,
    AuditorPanelProvider::class,
    FakultasPanelProvider::class,
    ProdiPanelProvider::class,
    SuperAdminPanelProvider::class,
];
