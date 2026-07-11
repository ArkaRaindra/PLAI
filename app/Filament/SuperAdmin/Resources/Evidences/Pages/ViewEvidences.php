<?php

namespace App\Filament\SuperAdmin\Resources\Evidences\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceLinks\EvidenceLinkResource;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewEvidences extends ViewRecord
{
    protected static string $resource = EvidencesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(fn (): string => route('filament.super-admin.resources.evidences.index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            Action::make('manageReviews')
                ->label('Kelola Review')
                ->icon(Heroicon::ClipboardDocumentCheck)
                ->color('gray')
                ->url(fn (): string => EvidenceReviewResource::getListUrl($this->record->id)),
            Action::make('manageLinks')
                ->label('Kelola Link')
                ->icon(Heroicon::Link)
                ->color('gray')
                ->url(fn (): string => EvidenceLinkResource::getListUrl($this->record->id)),
            EditAction::make(),
            EvidencesResource::submitAction(),
            EvidencesResource::startReviewAction(),
            EvidencesResource::approveAction(),
            EvidencesResource::rejectAction(),
            EvidencesResource::publishAction(),
        ];
    }
}
