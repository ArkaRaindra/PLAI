<?php

namespace App\Filament\SuperAdmin\Resources\Users\Pages;

use App\Filament\SuperAdmin\Resources\Users\UserResource;
use App\Models\Faculty;
use App\Models\StudyProgram;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $faculty = Faculty::find($data['faculty_id']);
        $studyProgram = StudyProgram::find($data['study_program_id']);

        $data['faculty'] = $faculty?->name;
        $data['study_program'] = $studyProgram?->name;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }
}
