<?php

namespace App\Filament\SuperAdmin\Resources\StandarVersions\Pages;

use App\Filament\SuperAdmin\Resources\StandarVersions\StandarVersionsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewStandarVersions extends ViewRecord
{
    protected static string $resource = StandarVersionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit')
                ->url($this->getResource()::getUrl('edit', ['record' => $this->record]))
                ->button()
                ->color('warning')
                ->icon(Heroicon::Pencil),
        ];
    }
}
