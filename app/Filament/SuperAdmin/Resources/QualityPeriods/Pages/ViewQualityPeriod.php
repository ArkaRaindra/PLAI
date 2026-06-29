<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriods\Pages;

use App\Filament\SuperAdmin\Resources\QualityPeriods\QualityPeriodResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewQualityPeriod extends ViewRecord
{
    protected static string $resource = QualityPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
           Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            EditAction::make()->icon(Heroicon::Pencil),
        ];
    }
}
