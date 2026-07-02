<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorOwners\Pages;

use App\Filament\SuperAdmin\Resources\IndicatorOwners\IndicatorOwnerResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIndicatorOwner extends ViewRecord
{
    protected static string $resource = IndicatorOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->icon('heroicon-o-pencil'),
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(IndicatorOwnerResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
