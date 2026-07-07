<?php

namespace App\Filament\SuperAdmin\Resources\Positions\Pages;

use App\Filament\SuperAdmin\Resources\Positions\PositionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListPositions extends ListRecords
{
    protected static string $resource = PositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::Plus),
        ];
    }
}
