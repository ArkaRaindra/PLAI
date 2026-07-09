<?php

namespace App\Filament\SuperAdmin\Resources\Evidences\Pages;

use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEvidences extends EditRecord
{
    protected static string $resource = EvidencesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
