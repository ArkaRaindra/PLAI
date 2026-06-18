<?php

namespace App\Filament\SuperAdmin\Resources\Indicators\Pages;

use App\Filament\SuperAdmin\Resources\Indicators\IndicatorResource;
use Filament\Resources\Pages\ListRecords;

class ListIndicators extends ListRecords
{
    protected static string $resource = IndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
