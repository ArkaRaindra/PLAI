<?php

namespace App\Filament\SuperAdmin\Resources\Periods\Pages;

use App\Filament\SuperAdmin\Resources\Periods\PeriodResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePeriod extends CreateRecord
{
    protected static string $resource = PeriodResource::class;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
}
