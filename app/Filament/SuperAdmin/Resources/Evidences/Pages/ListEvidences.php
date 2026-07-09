<?php

namespace App\Filament\SuperAdmin\Resources\Evidences\Pages;

use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvidences extends ListRecords
{
    protected static string $resource = EvidencesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
