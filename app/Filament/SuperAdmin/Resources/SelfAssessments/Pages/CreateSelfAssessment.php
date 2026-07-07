<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Pages;

use App\Filament\SuperAdmin\Resources\SelfAssessments\SelfAssessmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSelfAssessment extends CreateRecord
{
    protected static string $resource = SelfAssessmentResource::class;

    protected static ?string $title = 'Tambah Self Assessment';

    protected static bool $canCreateAnother = false;

    protected function fillForm(): void
    {
        $this->form->fill([
            'status' => 'draft',
        ]);
    }
}
