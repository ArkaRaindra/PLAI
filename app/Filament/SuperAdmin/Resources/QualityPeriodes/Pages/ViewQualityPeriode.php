<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriodes\Pages;

use App\Filament\SuperAdmin\Resources\QualityPeriodes\QualityPeriodeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQualityPeriode extends ViewRecord
{
    protected static string $resource = QualityPeriodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
