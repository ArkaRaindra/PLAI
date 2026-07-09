<?php

namespace App\Filament\SuperAdmin\Resources\Evidences\Pages;

use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEvidences extends ViewRecord
{
    protected static string $resource = EvidencesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
