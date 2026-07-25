<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Pages;

use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\CorrectiveActionResource;
use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\CorrectiveActionUpdateResource;
use App\Models\CorrectiveAction;
use App\Models\CorrectiveActionUpdate;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Url;

class ListCorrectiveActionUpdates extends Page
{
    #[Url]
    public ?string $correctiveActionId = null;

    protected static string $resource = CorrectiveActionUpdateResource::class;

    protected static ?string $title = 'Progress Timeline';

    protected string $view = 'filament.super-admin.resources.corrective-action-updates.pages.list-corrective-action-updates';

    public ?CorrectiveAction $correctiveAction = null;

    public function mount(): void
    {
        if (filled($this->correctiveActionId)) {
            $this->correctiveAction = CorrectiveAction::query()
                ->with(['auditFinding', 'ownerPosition.user'])
                ->find($this->correctiveActionId);
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Progress Timeline',
        ];
    }

    public function timelineEntries(): Collection
    {
        if (blank($this->correctiveAction)) {
            return new Collection;
        }

        return CorrectiveActionUpdate::query()
            ->where('corrective_action_id', $this->correctiveAction->id)
            ->with(['updater', 'evidenceLink.evidence'])
            ->orderByDesc('id')
            ->get();
    }

    public function latestProgress(): int
    {
        return $this->correctiveAction?->latestProgressPercentage() ?? 0;
    }

    protected function getHeaderActions(): array
    {
        if ($this->correctiveAction === null) {
            return [
                Action::make('back')
                    ->label('Kembali')
                    ->url(AuditFindingResource::getUrl('index'))
                    ->button()
                    ->color('gray')
                    ->icon(Heroicon::ArrowLeft),
            ];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(CorrectiveActionResource::getUrl('view', ['record' => $this->correctiveActionId]))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            CreateAction::make()
                ->label('Tambah Progress')
                ->icon(Heroicon::Plus)
                ->url(CorrectiveActionUpdateResource::getCreateUrl($this->correctiveActionId))
                ->visible(fn (): bool => $this->correctiveAction?->status === 'submitted'),
        ];
    }
}
