<?php

namespace App\Filament\Prodi\Resources\Standards\Pages;

use App\Filament\Prodi\Resources\Standards\StandardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStandard extends EditRecord
{
    protected static string $resource = StandardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
