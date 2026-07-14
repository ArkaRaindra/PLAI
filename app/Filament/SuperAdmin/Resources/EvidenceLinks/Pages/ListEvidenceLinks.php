<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceLinks\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceLinks\EvidenceLinkResource;
use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use App\Models\EvidenceLinks;
use App\Models\Evidences;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListEvidenceLinks extends ListRecords
{
    #[Url]
    public ?string $evidenceId = null;

    protected static string $resource = EvidenceLinkResource::class;

    protected static ?string $title = 'Evidence Link';

    protected static ?string $breadcrumb = 'Evidence Link';

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
            EvidencesResource::getUrl('view', ['record' => $evidence->id]) => $evidence->title,
            EvidenceLinkResource::getListUrl($evidence->id) => 'Evidence Link',
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (blank($this->evidenceId)) {
            return EvidenceLinks::query()->whereRaw('1 = 0');
        }

        return EvidenceLinks::query()
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
                ->label('Tautkan Item')
                ->icon(Heroicon::Plus)
                ->authorize(fn (): bool => auth()->user()?->can('create', EvidenceLinks::class) ?? false)
                ->url(EvidenceLinkResource::getCreateUrl($this->evidenceId)),
        ];
    }
}
