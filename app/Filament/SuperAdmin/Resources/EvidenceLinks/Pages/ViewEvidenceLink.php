<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceLinks\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceLinks\EvidenceLinkResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEvidenceLink extends ViewRecord
{
    protected static string $resource = EvidenceLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
