<?php

namespace App\Filament\SuperAdmin\Resources\UserPositions\Pages;

use App\Filament\SuperAdmin\Resources\UserPositions\UserPositionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserPositions extends ListRecords
{
    protected static string $resource = UserPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
