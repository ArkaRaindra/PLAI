<?php

namespace App\Filament\SuperAdmin\Resources\StudyPrograms\Pages;

use App\Filament\SuperAdmin\Resources\StudyPrograms\StudyProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudyProgram extends EditRecord
{
    protected static string $resource = StudyProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
