<?php

namespace App\Filament\SuperAdmin\Resources\Targets\Pages;

use App\Filament\SuperAdmin\Resources\Targets\TargetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTargets extends ListRecords
{
    protected static string $resource = TargetResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->withCount('realizations');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
