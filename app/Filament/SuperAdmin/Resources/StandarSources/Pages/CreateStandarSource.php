<?php

namespace App\Filament\SuperAdmin\Resources\StandarSources\Pages;

use App\Filament\SuperAdmin\Resources\StandarSources\StandarSourceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateStandarSource extends CreateRecord
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
        ];
    }
}
