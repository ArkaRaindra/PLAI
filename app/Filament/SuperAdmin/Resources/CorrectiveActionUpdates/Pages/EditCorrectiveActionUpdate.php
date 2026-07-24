<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Pages;

use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\CorrectiveActionUpdateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCorrectiveActionUpdate extends EditRecord
{
    protected static string $resource = CorrectiveActionUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
