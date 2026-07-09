<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceVersions\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceVersions\EvidenceVersionsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvidenceVersions extends ListRecords
{
    protected static string $resource = EvidenceVersionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
