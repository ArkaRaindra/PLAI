<?php

namespace App\Filament\SuperAdmin\Resources\Periods\Pages;

use App\Filament\SuperAdmin\Resources\Periods\PeriodResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPeriod extends EditRecord
{
    protected static string $resource = PeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return PeriodResource::getUrl('index');
    }
}
