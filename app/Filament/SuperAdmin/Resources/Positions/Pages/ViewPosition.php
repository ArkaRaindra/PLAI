<?php

namespace App\Filament\SuperAdmin\Resources\Positions\Pages;

use App\Filament\SuperAdmin\Resources\Positions\PositionResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewPosition extends ViewRecord
{
    protected static string $resource = PositionResource::class;

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
