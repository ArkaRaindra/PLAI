<?php

namespace App\Filament\SuperAdmin\Resources\StandarVersions\Pages;

use App\Filament\SuperAdmin\Resources\StandarVersions\StandarVersionsResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewStandarVersions extends ViewRecord
{
    protected static string $resource = StandarVersionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
            EditAction::make()
                ->label('Edit')
                ->url($this->getResource()::getUrl('edit', ['record' => $this->record]))
                ->button()
                ->color('warning')
                ->icon('heroicon-o-pencil'),
        ];
    }
}
