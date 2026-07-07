<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Pages;

use App\Filament\SuperAdmin\Resources\SelfAssessments\SelfAssessmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSelfAssessment extends CreateRecord
{
    protected static string $resource = SelfAssessmentResource::class;
}
