<?php

namespace App\Filament\SuperAdmin\Resources\UserPositions\Pages;

use App\Filament\SuperAdmin\Resources\UserPositions\UserPositionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserPosition extends CreateRecord
{
    protected static string $resource = UserPositionResource::class;
}
