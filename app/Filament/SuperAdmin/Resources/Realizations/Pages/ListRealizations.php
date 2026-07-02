<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Pages;

use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use App\Filament\SuperAdmin\Resources\Targets\TargetResource;
use App\Models\Realization;
use App\Models\Target;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListRealizations extends ListRecords
{
    #[Url]
    public ?string $targetId = null;

    protected static string $resource = RealizationResource::class;

    protected static ?string $title = 'Realisasi';

    protected static ?string $breadcrumb = 'Realisasi';

    public function mount(): void
    {
        if (blank($this->targetId)) {
            $this->redirect(TargetResource::getUrl('index'));

            return;
        }

        parent::mount();
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        if (blank($this->targetId)) {
            return parent::getBreadcrumbs();
        }

        $target = Target::query()
            ->with(['indicator:id,name', 'qualityPeriod:id,code'])
            ->find($this->targetId);

        if ($target === null) {
            return parent::getBreadcrumbs();
        }

        $targetLabel = trim(($target->indicator?->name ?? '').' — '.($target->qualityPeriod?->code ?? ''));

        return [
            TargetResource::getUrl('index') => TargetResource::getNavigationLabel(),
            RealizationResource::getListUrl($target->id) => $targetLabel,
            'Realisasi',
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (blank($this->targetId)) {
            return Realization::query()->whereRaw('1 = 0');
        }

        return Realization::query()
            ->where('target_id', (int) $this->targetId);
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->targetId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(TargetResource::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            CreateAction::make()
                ->label('Tambah Realisasi')
                ->icon(Heroicon::Plus)
                ->url(RealizationResource::getCreateUrl($this->targetId)),
        ];
    }
}
