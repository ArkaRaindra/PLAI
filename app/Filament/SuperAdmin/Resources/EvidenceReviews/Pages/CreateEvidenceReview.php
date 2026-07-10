<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use App\Models\Evidences;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

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
}
