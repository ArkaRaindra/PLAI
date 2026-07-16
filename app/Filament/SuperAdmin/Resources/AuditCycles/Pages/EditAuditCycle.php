<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Pages;

use App\Filament\SuperAdmin\Resources\AuditCycles\AuditCycleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAuditCycle extends EditRecord
{
    protected static string $resource = AuditCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (\RuntimeException $exception) {
            Notification::make()
                ->title('Perubahan status ditolak')
                ->body($exception->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
