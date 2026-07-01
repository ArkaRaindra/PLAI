<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorMappings\Pages;

use App\Filament\SuperAdmin\Resources\IndicatorMappings\IndicatorMappingResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateIndicatorMapping extends CreateRecord
{
    protected static string $resource = IndicatorMappingResource::class;

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
