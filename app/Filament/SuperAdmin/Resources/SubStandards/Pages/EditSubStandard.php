<?php

namespace App\Filament\SuperAdmin\Resources\SubStandards\Pages;

use App\Filament\SuperAdmin\Resources\SubStandards\SubStandardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubStandard extends EditRecord
{
    protected static string $resource = SubStandardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return SubStandardResource::getUrl('index');
    }
}
