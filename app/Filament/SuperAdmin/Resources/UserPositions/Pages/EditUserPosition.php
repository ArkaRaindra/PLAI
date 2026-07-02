<?php

namespace App\Filament\SuperAdmin\Resources\UserPositions\Pages;

use App\Filament\SuperAdmin\Resources\UserPositions\UserPositionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUserPosition extends EditRecord
{
    protected static string $resource = UserPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
