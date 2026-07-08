<?php

namespace App\Filament\SuperAdmin\Resources\DataTrails\Pages;

use App\Filament\SuperAdmin\Resources\DataTrails\DataTrailsResource;
use Filament\Resources\Pages\ViewRecord;

class ViewDataTrails extends ViewRecord
{
    protected static string $resource = DataTrailsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
