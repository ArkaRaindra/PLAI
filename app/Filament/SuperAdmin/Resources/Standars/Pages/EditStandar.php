<?php

namespace App\Filament\SuperAdmin\Resources\Standars\Pages;

use App\Filament\SuperAdmin\Resources\Standars\StandarResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStandar extends EditRecord
{
    protected static string $resource = StandarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
