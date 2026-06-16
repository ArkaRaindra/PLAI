<?php

namespace App\Filament\Prodi\Resources\SubStandards\Pages;

use App\Filament\Prodi\Resources\SubStandards\SubStandardResource;
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
}
