<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditEvidenceReview extends EditRecord
{
    protected static string $resource = EvidenceReviewResource::class;

    protected static ?string $title = 'Ubah Review Evidence';

    protected static ?string $breadcrumb = 'Ubah Review Evidence';

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            EvidenceReviewResource::getListUrl($this->record->evidence_id) => 'Review Evidence',
            'Ubah Review Evidence',
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
            ViewAction::make(),
            DeleteAction::make()
                ->icon(Heroicon::Trash)
                ->authorize(fn (): bool => auth()->user()?->can('delete', $this->record) ?? false)
                ->successRedirectUrl(EvidenceReviewResource::getListUrl($this->record->evidence_id)),
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
        return EvidenceReviewResource::getUrl('view', ['record' => $this->record]);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(EvidenceReviewResource::getUrl('view', ['record' => $this->record]));
    }
}