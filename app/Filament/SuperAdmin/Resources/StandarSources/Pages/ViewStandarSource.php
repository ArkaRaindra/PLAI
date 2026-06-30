<?php

namespace App\Filament\SuperAdmin\Resources\StandarSources\Pages;

use App\Filament\SuperAdmin\Resources\StandarSources\StandarSourceResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewStandarSource extends ViewRecord
{
    protected static string $resource = StandarSourceResource::class;

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
