<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorMappings\Pages;

use App\Filament\SuperAdmin\Resources\IndicatorMappings\IndicatorMappingResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewIndicatorMapping extends ViewRecord
{
    protected static string $resource = IndicatorMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            EditAction::make()->icon(Heroicon::PencilSquare),
        ];
    }
}
