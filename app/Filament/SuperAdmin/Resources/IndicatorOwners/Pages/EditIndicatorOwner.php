<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorOwners\Pages;

use App\Filament\SuperAdmin\Resources\IndicatorOwners\IndicatorOwnerResource;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditIndicatorOwner extends EditRecord
{
    protected static string $resource = IndicatorOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(IndicatorOwnerResource::getUrl('index'))
                ->color('gray'),
            ViewAction::make()->icon('heroicon-o-eye'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
