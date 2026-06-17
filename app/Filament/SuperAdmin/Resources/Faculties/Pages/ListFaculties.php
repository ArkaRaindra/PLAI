<?php

namespace App\Filament\SuperAdmin\Resources\Faculties\Pages;

use App\Filament\SuperAdmin\Resources\Faculties\FacultyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFaculties extends ListRecords
{
    protected static string $resource = FacultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
