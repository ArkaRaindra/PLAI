<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorMappings\Pages;

use App\Filament\SuperAdmin\Resources\IndicatorMappings\IndicatorMappingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListIndicatorMappings extends ListRecords
{
    protected static string $resource = IndicatorMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon(Heroicon::Plus),
        ];
    }
}
