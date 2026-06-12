<?php

namespace App\Filament\SuperAdmin\Resources\SubStandards\Pages;

use App\Filament\SuperAdmin\Resources\SubStandards\SubStandardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubStandards extends ListRecords
{
    protected static string $resource = SubStandardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
