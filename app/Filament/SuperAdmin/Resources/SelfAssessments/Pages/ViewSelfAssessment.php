<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Pages;

use App\Filament\SuperAdmin\Resources\SelfAssessments\SelfAssessmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSelfAssessment extends ViewRecord
{
    protected static string $resource = SelfAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn ($record) => in_array($record->status, ['draft', 'rejected'], true)),
            SelfAssessmentResource::submitAction(),
            SelfAssessmentResource::approveAction(),
            SelfAssessmentResource::rejectAction(),
        ];
    }
}
