<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriodes\Pages;

use App\Filament\SuperAdmin\Resources\QualityPeriodes\QualityPeriodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQualityPeriodes extends ListRecords
{
    protected static string $resource = QualityPeriodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
