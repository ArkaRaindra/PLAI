<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceLinks\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceLinks\EvidenceLinkResource;
use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use App\Models\Evidences;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;

class CreateEvidenceLink extends CreateRecord
{
    #[Url]
    public ?string $evidenceId = null;

    protected static string $resource = EvidenceLinkResource::class;

    protected static ?string $title = 'Tautkan Item';

    protected static ?string $breadcrumb = 'Tautkan Item';

    protected static bool $canCreateAnother = true;

    public function mount(): void
    {
        if (blank($this->evidenceId)) {
            $this->redirect(EvidencesResource::getUrl('index'));

            return;
        }

        parent::mount();
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        if (blank($this->evidenceId)) {
            return parent::getBreadcrumbs();
        }

        $evidence = Evidences::query()->find($this->evidenceId);

        if ($evidence === null) {
            return parent::getBreadcrumbs();
        }

        return [
            EvidencesResource::getUrl('index') => EvidencesResource::getNavigationLabel(),
            EvidenceLinkResource::getListUrl($evidence->id) => $evidence->title,
            'Tautkan Item',
        ];
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->evidenceId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(EvidenceLinkResource::getListUrl($this->evidenceId))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'evidence_id' => (int) $this->evidenceId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['evidence_id'] = (int) $this->evidenceId;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return EvidenceLinkResource::getListUrl($this->evidenceId);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(EvidenceLinkResource::getListUrl($this->evidenceId));
    }
}