<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceVersions\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceVersions\EvidenceVersionsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEvidenceVersions extends ViewRecord
{
    protected static string $resource = EvidenceVersionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
