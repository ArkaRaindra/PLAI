<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorOwners\Pages;

use App\Filament\SuperAdmin\Resources\IndicatorOwners\IndicatorOwnerResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateIndicatorOwner extends CreateRecord
{
    protected static string $resource = IndicatorOwnerResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(IndicatorOwnerResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
