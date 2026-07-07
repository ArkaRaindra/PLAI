<?php

namespace App\Filament\SuperAdmin\Resources\StandarVersions\Pages;

use App\Filament\SuperAdmin\Resources\StandarVersions\StandarVersionsResource;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStandarVersions extends EditRecord
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

            ViewAction::make()
                ->label('Lihat')
                ->url($this->getResource()::getUrl('view', ['record' => $this->record]))
                ->button()
                ->color('info')
                ->icon('heroicon-o-eye'),
        ];
    }
}
