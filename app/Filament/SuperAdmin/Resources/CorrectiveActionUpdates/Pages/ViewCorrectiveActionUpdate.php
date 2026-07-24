<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Pages;

use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\CorrectiveActionUpdateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCorrectiveActionUpdate extends ViewRecord
{
    protected static string $resource = CorrectiveActionUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
