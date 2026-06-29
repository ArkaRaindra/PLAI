<?php

namespace App\Filament\SuperAdmin\Resources\OrgUnits\Pages;

use App\Filament\SuperAdmin\Resources\OrgUnits\OrgUnitResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOrgUnit extends EditRecord
{
    protected static string $resource = OrgUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
