<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Pages;

use App\Filament\SuperAdmin\Resources\SelfAssessments\SelfAssessmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSelfAssessment extends EditRecord
{
    protected static string $resource = SelfAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
