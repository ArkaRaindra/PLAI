<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceVersions\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceVersions\EvidenceVersionsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEvidenceVersions extends EditRecord
{
    protected static string $resource = EvidenceVersionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
