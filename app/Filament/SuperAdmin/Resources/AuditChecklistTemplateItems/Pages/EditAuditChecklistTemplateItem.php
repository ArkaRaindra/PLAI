<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Pages;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\AuditChecklistTemplateItemResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAuditChecklistTemplateItem extends EditRecord
{
    protected static string $resource = AuditChecklistTemplateItemResource::class;

    protected static ?string $title = 'Ubah Item Checklist';

    public function getBreadcrumbs(): array
    {
        return [
            AuditChecklistTemplateItemResource::getListUrl($this->record->template_id) => 'Item Checklist',
            'Ubah Item Checklist',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditChecklistTemplateItemResource::getListUrl($this->record->template_id))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            DeleteAction::make()
                ->successRedirectUrl(AuditChecklistTemplateItemResource::getListUrl($this->record->template_id)),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['template_id'] = $this->record->template_id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return AuditChecklistTemplateItemResource::getListUrl($this->record->template_id);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(AuditChecklistTemplateItemResource::getListUrl($this->record->template_id));
    }
}
