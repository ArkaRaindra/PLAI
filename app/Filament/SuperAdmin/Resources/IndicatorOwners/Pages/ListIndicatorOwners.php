<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorOwners\Pages;

use App\Filament\SuperAdmin\Resources\IndicatorOwners\IndicatorOwnerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIndicatorOwners extends ListRecords
{
    protected static string $resource = IndicatorOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon('heroicon-o-plus'),
        ];
    }
}
