<?php

namespace App\Filament\SuperAdmin\Resources\Standards\Pages;

use App\Filament\SuperAdmin\Resources\Standards\StandardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStandard extends CreateRecord
{
    protected static string $resource = StandardResource::class;
    
    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
}
