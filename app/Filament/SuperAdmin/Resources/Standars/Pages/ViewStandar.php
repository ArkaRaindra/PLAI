<?php

namespace App\Filament\SuperAdmin\Resources\Standars\Pages;

use App\Filament\SuperAdmin\Resources\Standars\StandarResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStandar extends ViewRecord
{
    protected static string $resource = StandarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
