<?php

namespace App\Filament\SuperAdmin\Resources\StandarVersions\Pages;

use App\Filament\SuperAdmin\Resources\StandarVersions\StandarVersionsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStandarVersions extends ViewRecord
{
    protected static string $resource = StandarVersionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
