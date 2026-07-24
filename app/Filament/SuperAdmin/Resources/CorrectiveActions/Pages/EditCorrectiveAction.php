<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages;

use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\CorrectiveActionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditCorrectiveAction extends EditRecord
{
    protected static string $resource = CorrectiveActionResource::class;

    protected static ?string $title = 'Ubah Corrective Action';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (! $this->record->isEditable()) {
            Notification::make()
                ->title('Corrective action ini sudah tidak dapat diubah')
                ->warning()
                ->send();

            $this->redirect(CorrectiveActionResource::getUrl('view',['record' => $this->record->id]));
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Ubah Corrective Action',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditFindingResource::getUrl('view', ['record' => $this->record->audit_finding_id]))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            ViewAction::make(),
            DeleteAction::make()
                ->successRedirectUrl(AuditFindingResource::getUrl('view', ['record' => $this->record->audit_finding_id])),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['audit_finding_id'] = $this->record->audit_finding_id;
        $data['organization_unit_id'] = $this->record->organization_unit_id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return CorrectiveActionResource::getUrl('view', ['record' => $this->record->id]);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(CorrectiveActionResource::getUrl('view', ['record' => $this->record->id]));   
    }
}
