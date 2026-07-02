<?php

namespace App\Filament\SuperAdmin\Resources\StandarVersions\Pages;

use App\Filament\SuperAdmin\Resources\StandarVersions\StandarVersionsResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateStandarVersions extends CreateRecord
{
    protected static string $resource = StandarVersionsResource::class;

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
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
