<?php

namespace App\Filament\SuperAdmin\Resources\StandarSources\Pages;

use App\Filament\SuperAdmin\Resources\StandarSources\StandarSourceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListStandarSources extends ListRecords
{
    protected static string $resource = StandarSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon(Heroicon::Plus),
        ];
    }
}
