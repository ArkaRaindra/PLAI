<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use App\Models\EvidenceReview;
use App\Models\Evidences;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListEvidenceReviews extends ListRecords
{
    #[Url]
    public ?string $evidenceId = null;

    protected static string $resource = EvidenceReviewResource::class;

    protected static ?string $title = 'Review Evidence';

    protected static ?string $breadcrumb = 'Review Evidence';

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
            EvidencesResource::getUrl('view', ['record' => $evidence->id]) => $evidence->title,
            EvidenceReviewResource::getListUrl($evidence->id) => 'Review Evidence',
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (blank($this->evidenceId)) {
            return EvidenceReview::query()->whereRaw('1 = 0');
        }

        return EvidenceReview::query()
            ->where('evidence_id', (int) $this->evidenceId);
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->evidenceId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(EvidencesResource::getUrl('view', ['record' => $this->evidenceId]))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            CreateAction::make()
                ->label('Tugaskan Reviewer')
                ->icon(Heroicon::Plus)
                ->authorize(fn (): bool => auth()->user()?->can('create', EvidenceReview::class) ?? false)
                ->url(EvidenceReviewResource::getCreateUrl($this->evidenceId)),
        ];
    }
}
