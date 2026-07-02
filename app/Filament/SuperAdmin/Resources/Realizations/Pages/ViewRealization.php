<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Pages;

use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRealization extends ViewRecord
{
    protected static string $resource = RealizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
