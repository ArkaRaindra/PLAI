<?php

namespace App\Filament\SuperAdmin\Resources\Targets\Pages;

use App\Filament\SuperAdmin\Resources\Targets\TargetResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTarget extends ViewRecord
{
    protected static string $resource = TargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
