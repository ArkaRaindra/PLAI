<?php

namespace App\Filament\SuperAdmin\Resources\Roles\Pages;

use App\Filament\SuperAdmin\Resources\Roles\RoleResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(RoleResource::getUrl('index'))
                ->color('gray'),
            DeleteAction::make()->icon('heroicon-o-trash'),
        ];
    }
}
