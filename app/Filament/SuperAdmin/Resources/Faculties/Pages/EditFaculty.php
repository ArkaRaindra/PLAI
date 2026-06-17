<?php

namespace App\Filament\SuperAdmin\Resources\Faculties\Pages;

use App\Filament\SuperAdmin\Resources\Faculties\FacultyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFaculty extends EditRecord
{
    protected static string $resource = FacultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
