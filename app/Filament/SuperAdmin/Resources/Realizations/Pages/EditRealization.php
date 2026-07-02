<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Pages;

use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRealization extends EditRecord
{
    protected static string $resource = RealizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
