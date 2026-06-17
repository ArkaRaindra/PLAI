<?php

namespace App\Filament\SuperAdmin\Resources\StudyPrograms\Pages;

use App\Filament\SuperAdmin\Resources\StudyPrograms\StudyProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudyPrograms extends ListRecords
{
    protected static string $resource = StudyProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
