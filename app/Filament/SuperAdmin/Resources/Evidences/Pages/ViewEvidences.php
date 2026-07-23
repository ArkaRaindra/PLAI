<?php

namespace App\Filament\SuperAdmin\Resources\Evidences\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceLinks\EvidenceLinkResource;
use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use App\Models\Evidences;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ViewEvidences extends ViewRecord
{
    protected static string $resource = EvidencesResource::class;

    protected function getHeaderActions(): array
    {
        $isSuperAdmin = Auth::user()?->hasRole('super-admin') ?? false;

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(fn (): string => route('filament.super-admin.resources.evidences.index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            Action::make('manageLinks')
                ->label('Kelola Link')
                ->icon(Heroicon::Link)
                ->color('gray')
                ->url(fn (): string => EvidenceLinkResource::getListUrl($this->record->id)),
            EditAction::make(),
            EvidencesResource::submitAction()->visible(fn (Evidences $record): bool => $isSuperAdmin || $record->workflowInstance?->current_status === 'draft'),
            EvidencesResource::reviseAction()->visible(fn (Evidences $record): bool => $isSuperAdmin || $record->workflowInstance?->current_status === 'rejected'),
            EvidencesResource::publishAction()->visible(fn (Evidences $record): bool => $isSuperAdmin || $record->workflowInstance?->current_status === 'approved'),
        ];
    }
}
