<?php

namespace App\Filament\SuperAdmin\Resources\Standars\Pages;

use App\Filament\SuperAdmin\Resources\Standars\StandarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStandars extends ListRecords
{
    protected static string $resource = StandarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
