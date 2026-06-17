<?php

namespace App\Filament\Prodi\Resources\AuditEvidence\Pages;

use App\Filament\Prodi\Resources\AuditEvidence\AuditEvidenceResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAuditEvidence extends EditRecord
{
    protected static string $resource = AuditEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submitToAuditor')
                ->label('Kirim ke auditor')
                ->requiresConfirmation()
                ->modalHeading('Kirim bukti audit ke auditor?')
                ->visible(fn (): bool => in_array($this->getRecord()->status, ['draft', 'returned'], true))
                ->action(function (): void {
                    $record = $this->getRecord();
                    $record->update(['status' => 'submitted']);

                    Notification::make()
                        ->title('Bukti audit berhasil dikirim ke auditor.')
                        ->success()
                        ->send();

                    $this->redirect(AuditEvidenceResource::getUrl('index'));
                }),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return AuditEvidenceResource::getUrl('index');
    }
}
