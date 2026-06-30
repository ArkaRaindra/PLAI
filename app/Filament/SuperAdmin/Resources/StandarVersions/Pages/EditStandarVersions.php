<?php

namespace App\Filament\SuperAdmin\Resources\StandarVersions\Pages;

use App\Filament\SuperAdmin\Resources\StandarVersions\StandarVersionsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStandarVersions extends EditRecord
{
    protected static string $resource = StandarVersionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
