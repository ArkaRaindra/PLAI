<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Pages;

use App\Filament\SuperAdmin\Resources\SelfAssessments\SelfAssessmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSelfAssessments extends ListRecords
{
    protected static string $resource = SelfAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
