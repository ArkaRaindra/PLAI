<?php

namespace App\Filament\SuperAdmin\Resources\SubStandards\Pages;

use App\Filament\SuperAdmin\Resources\SubStandards\SubStandardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubStandard extends CreateRecord
{
    protected static string $resource = SubStandardResource::class;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
}
