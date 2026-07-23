<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Pages;

use App\Filament\SuperAdmin\Resources\AuditCycles\AuditCycleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListAuditCycles extends ListRecords
{
    protected static string $resource = AuditCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::Plus),
        ];
    }
}
