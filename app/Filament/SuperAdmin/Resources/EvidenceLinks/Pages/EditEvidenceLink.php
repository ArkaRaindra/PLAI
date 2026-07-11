<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceLinks\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceLinks\EvidenceLinkResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditEvidenceLink extends EditRecord
{
    protected static string $resource = EvidenceLinkResource::class;

    protected static ?string $title = 'Ubah Evidence Link';

    protected static ?string $breadcrumb = 'Ubah Evidence Link';

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            EvidenceLinkResource::getListUrl($this->record->evidence_id) => 'Evidence Link',
            'Ubah Evidence Link',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(EvidenceLinkResource::getListUrl($this->record->evidence_id))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            DeleteAction::make()
                ->icon(Heroicon::Trash)
                ->authorize(fn (): bool => auth()->user()?->can('delete', $this->record) ?? false)
                ->successRedirectUrl(EvidenceLinkResource::getListUrl($this->record->evidence_id)),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['evidence_id'] = $this->record->evidence_id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return EvidenceLinkResource::getListUrl($this->record->evidence_id);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(EvidenceLinkResource::getListUrl($this->record->evidence_id));
    }
}