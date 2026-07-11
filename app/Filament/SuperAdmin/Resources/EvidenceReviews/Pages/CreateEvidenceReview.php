<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use App\Models\Evidences;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;

class CreateEvidenceReview extends CreateRecord
{
    #[Url]
    public ?string $evidenceId = null;

    protected static string $resource = EvidenceReviewResource::class;

    protected static ?string $title = 'Tugaskan Reviewer';

    protected static ?string $breadcrumb = 'Tugaskan Reviewer';

    protected static bool $canCreateAnother = false;

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
            EvidenceReviewResource::getListUrl($evidence->id) => $evidence->title,
            'Tugaskan Reviewer',
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
                ->url(EvidenceReviewResource::getListUrl($this->evidenceId))
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
        $data['status'] = 'pending';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return EvidenceReviewResource::getUrl('view', ['record' => $this->record]);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(EvidenceReviewResource::getListUrl($this->evidenceId));
    }
}