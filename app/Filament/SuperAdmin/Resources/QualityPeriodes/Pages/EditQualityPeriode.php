<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriodes\Pages;

use App\Filament\SuperAdmin\Resources\QualityPeriodes\QualityPeriodeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditQualityPeriode extends EditRecord
{
    protected static string $resource = QualityPeriodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
