<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Pages;

use App\Filament\SuperAdmin\Resources\AuditCycles\AuditCycleResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAuditCycle extends CreateRecord
{
    protected static string $resource = AuditCycleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (\RuntimeException $exception) {
            Notification::make()
                ->title('Tidak dapat membuat siklus audit')
                ->body($exception->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }
}