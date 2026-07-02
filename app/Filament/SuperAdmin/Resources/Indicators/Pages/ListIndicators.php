<?php

namespace App\Filament\SuperAdmin\Resources\Indicators\Pages;

use App\Filament\SuperAdmin\Pages\ManageStandards;
use App\Filament\SuperAdmin\Resources\Indicators\IndicatorResource;
use App\Filament\SuperAdmin\Resources\StandarSources\StandarSourceResource;
use App\Models\Indicator;
use App\Models\Standard;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListIndicators extends ListRecords
{
    #[Url]
    public ?string $standardId = null;

    protected static string $resource = IndicatorResource::class;

    protected static ?string $title = 'Indikator';

    protected static ?string $breadcrumb = 'Indikator';

    public function mount(): void
    {
        if (blank($this->standardId)) {
            $this->redirect(ManageStandards::getUrl());

            return;
        }

        parent::mount();
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        if (blank($this->standardId)) {
            return parent::getBreadcrumbs();
        }

        $standard = Standard::query()->find($this->standardId);

        if ($standard === null) {
            return parent::getBreadcrumbs();
        }

        $breadcrumbs = [
            StandarSourceResource::getUrl() => StandarSourceResource::getNavigationLabel(),
            ManageStandards::getUrl(['standardSourceId' => $standard->standard_source_id]) => $standard->standardSource?->name ?? 'Standar',
            IndicatorResource::getListUrl($standard->id) => $standard->code.' '.$standard->name,
        ];

        return [
            ...$breadcrumbs,
            'Indikator',
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (blank($this->standardId)) {
            return Indicator::query()->whereRaw('1 = 0');
        }

        return Indicator::query()
            ->whereHas('standardVersion', fn (Builder $query): Builder => $query
                ->where('standard_id', (int) $this->standardId));
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->standardId)) {
            return [];
        }

        $standard = Standard::query()->find($this->standardId);

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(ManageStandards::getUrl(['standardSourceId' => $standard?->standard_source_id]))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            CreateAction::make()
                ->label('Tambah Indikator')
                ->icon(Heroicon::Plus)
                ->url(IndicatorResource::getCreateUrl($this->standardId)),
        ];
    }
}
