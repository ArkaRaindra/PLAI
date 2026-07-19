<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Pages;

use App\Filament\SuperAdmin\Resources\AuditCycles\AuditCycleResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAuditCycle extends EditRecord
{
    protected static string $resource = AuditCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
            ViewAction::make(),
            Action::make('save_changes')
                ->label('Simpan')
                ->button()
                ->color('primary')
                ->action(fn () => $this->save())
                ->url($this->getResource()::getUrl('index')),
            DeleteAction::make()
                ->label('Hapus')
                ->icon(Heroicon::Trash),
        ];
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->hidden();
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->hidden();
    }
}
