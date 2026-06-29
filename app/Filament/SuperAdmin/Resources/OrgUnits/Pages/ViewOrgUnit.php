<?php

namespace App\Filament\SuperAdmin\Resources\OrgUnits\Pages;

use App\Filament\SuperAdmin\Resources\OrgUnits\OrgUnitResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrgUnit extends ViewRecord
{
    protected static string $resource = OrgUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
