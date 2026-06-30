<?php

namespace App\Filament\SuperAdmin\Resources\StandarSources\Pages;

use App\Filament\SuperAdmin\Resources\StandarSources\StandarSourceResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditStandarSource extends EditRecord
{
    protected static string $resource = StandarSourceResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            ViewAction::make()->icon(Heroicon::Eye)->color('info'),
            DeleteAction::make()->icon(Heroicon::Trash),
        ];
    }
}
