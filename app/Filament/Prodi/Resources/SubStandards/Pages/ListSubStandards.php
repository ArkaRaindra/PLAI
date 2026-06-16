<?php

namespace App\Filament\Prodi\Resources\SubStandards\Pages;

use App\Filament\Prodi\Resources\SubStandards\SubStandardResource;
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
