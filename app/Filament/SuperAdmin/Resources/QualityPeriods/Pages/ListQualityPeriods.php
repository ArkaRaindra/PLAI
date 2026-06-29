<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriods\Pages;

use App\Filament\SuperAdmin\Resources\QualityPeriods\QualityPeriodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQualityPeriods extends ListRecords
{
    protected static string $resource = QualityPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('Tambah Periode Kualitas'),
        ];
    }
}
