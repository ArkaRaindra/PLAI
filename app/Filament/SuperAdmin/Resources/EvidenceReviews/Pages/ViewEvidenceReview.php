<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use App\Models\EvidenceReview;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewEvidenceReview extends ViewRecord
{
    protected static string $resource = EvidenceReviewResource::class;

    protected static ?string $title = 'Detail Review Evidence';

    protected static ?string $breadcrumb = 'Detail Review Evidence';

    public function getBreadcrumbs(): array
    {
        return [
            EvidenceReviewResource::getListUrl($this->record->evidence_id) => 'Review Evidence',
            'Detail Review Evidence',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(EvidenceReviewResource::getListUrl($this->record->evidence_id))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            EditAction::make()
                ->authorize(fn (EvidenceReview $record): bool => auth()->user()?->can('update', $record) ?? false),
            EvidenceReviewResource::requestRevisionAction(),
            EvidenceReviewResource::approveAction(),
            EvidenceReviewResource::rejectAction(),
            EvidenceReviewResource::reopenAction(),
        ];
    }
}
