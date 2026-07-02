<?php

namespace App\Filament\SuperAdmin\Resources\UserPositions\Pages;

use App\Filament\SuperAdmin\Resources\UserPositions\UserPositionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUserPosition extends ViewRecord
{
    protected static string $resource = UserPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
