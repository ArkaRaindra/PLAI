<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorMappings\Pages;

use App\Filament\SuperAdmin\Resources\IndicatorMappings\IndicatorMappingResource;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditIndicatorMapping extends EditRecord
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
            ViewAction::make(),
        ];
    }
}
