<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Pages;

use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRealizations extends ListRecords
{
    protected static string $resource = RealizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
