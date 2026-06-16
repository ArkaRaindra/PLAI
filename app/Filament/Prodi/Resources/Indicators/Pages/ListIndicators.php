<?php

namespace App\Filament\Prodi\Resources\Indicators\Pages;

use App\Filament\Prodi\Resources\Indicators\IndicatorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIndicators extends ListRecords
{
    protected static string $resource = IndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
