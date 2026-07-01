<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Filament\SuperAdmin\Resources\Indicators\IndicatorResource;
use App\Filament\SuperAdmin\Resources\Standards\StandardResource;
use App\Filament\SuperAdmin\Resources\StandarSources\StandarSourceResource;
use App\Models\QualityPeriod;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\StandardVersion;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use LaraZeus\Tabler\Tabler;
use Livewire\Attributes\Url;
use Wsmallnews\FilamentNestedset\Pages\NestedsetPage;

class ManageStandards extends NestedsetPage
{
    #[Url]
    public ?string $standardSourceId = null;

    #[Url]
    public ?string $qualityPeriodId = null;

    #[Url]
    public ?string $standardVersionId = null;

    protected static ?string $model = Standard::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::ListTree;

    protected static string|\UnitEnum|null $navigationGroup = 'Penetapan';

    protected static ?string $navigationLabel = 'Standar';

    protected static ?string $modelLabel = 'Standar';

    protected static ?string $pluralModelLabel = 'Standar';

    protected static string $recordTitleAttribute = 'name';

    protected static bool $isScopedToTenant = false;

    protected static ?string $emptyLabel = 'Belum ada standar';

    protected static ?string $emptyTipLabel = 'Buat standar pertama untuk sumber ini.';

    protected string $view = 'filament.super-admin.pages.manage-standards';

    public function hasSelectedSource(): bool
    {
        return filled($this->standardSourceId);
    }

    public function updatedStandardSourceId(): void
    {
        $this->qualityPeriodId = null;
        $this->standardVersionId = null;
        $this->cachedHeaderActions = [];
        $this->cacheInteractsWithHeaderActions();
    }

    public function updatedQualityPeriodId(): void
    {
        if (blank($this->standardVersionId)) {
            return;
        }

        $exists = StandardVersion::query()
            ->whereKey((int) $this->standardVersionId)
            ->when(filled($this->qualityPeriodId), fn ($query) => $query->where('quality_period_id', (int) $this->qualityPeriodId))
            ->exists();

        if (! $exists) {
            $this->standardVersionId = null;
        }
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [
            StandarSourceResource::getUrl() => StandarSourceResource::getNavigationLabel(),
        ];

        if ($this->hasSelectedSource()) {
            $source = StandardSource::query()->find($this->standardSourceId);

            if ($source !== null) {
                $breadcrumbs[static::getUrl(['standardSourceId' => $this->standardSourceId])] = $source->name;
            }
        }

        return [
            ...$breadcrumbs,
            static::getNavigationLabel() ?? 'Standar',
        ];
    }

    public function createAction(): CreateAction
    {
        return CreateAction::make('create')
            ->label('Buat Standar')
            ->icon(Heroicon::Plus)
            ->url(fn (): string => StandardResource::getCreateUrl(
                $this->standardSourceId,
                qualityPeriodId: $this->qualityPeriodId,
                standardVersionId: $this->standardVersionId,
            ));
    }

    public function createChildAction(): CreateAction
    {
        return CreateAction::make('createChild')
            ->iconButton()
            ->size(Size::Large)
            ->iconSize(IconSize::Large)
            ->tooltip('Tambah Sub-standar')
            ->icon('heroicon-o-plus-circle')
            ->url(fn (array $arguments): string => StandardResource::getCreateUrl(
                $this->standardSourceId,
                $arguments['parentId'] ?? null,
                qualityPeriodId: $this->qualityPeriodId,
                standardVersionId: $this->standardVersionId,
            ));
    }

    public function editAction(): EditAction
    {
        return EditAction::make('edit')
            ->iconButton()
            ->size(Size::Large)
            ->iconSize(IconSize::Large)
            ->tooltip('Ubah')
            ->icon('heroicon-m-pencil-square')
            ->url(fn (array $arguments): string => StandardResource::getUrl('edit', [
                'record' => $arguments['id'],
            ]));
    }

    public function deleteAction(): DeleteAction
    {
        return parent::deleteAction()
            ->iconButton()
            ->size(Size::Large)
            ->iconSize(IconSize::Large)
            ->tooltip('Hapus');
    }

    public function indicatorsAction(): Action
    {
        return Action::make('indicators')
            ->iconButton()
            ->size(Size::Large)
            ->iconSize(IconSize::Large)
            ->tooltip('Indikator')
            ->icon(Heroicon::ChartBarSquare)
            ->url(fn (array $arguments): string => IndicatorResource::getCreateUrl($arguments['id']));
    }

    public function fixTreeAction(): Action
    {
        return parent::fixTreeAction()
            ->label('Perbaiki Tree')->color(Color::Gray);
    }

    /**
     * @return array<string, int>
     */
    public function nestedScoped(): array
    {
        if (! $this->hasSelectedSource()) {
            return [];
        }

        return ['standard_source_id' => (int) $this->standardSourceId];
    }

    public function getEloquentQuery($query)
    {
        if (! $this->hasSelectedSource()) {
            return $query->whereRaw('1 = 0');
        }

        $query = $query->withCount('indicators');

        if (filled($this->qualityPeriodId)) {
            $query->whereHas(
                'standardVersions',
                fn ($versionQuery) => $versionQuery->where('quality_period_id', (int) $this->qualityPeriodId),
            );
        }

        if (filled($this->standardVersionId)) {
            $query->whereHas(
                'standardVersions',
                fn ($versionQuery) => $versionQuery->whereKey((int) $this->standardVersionId),
            );
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        if (! $this->hasSelectedSource()) {
            return [];
        }

        return parent::getHeaderActions();
    }

    protected function getViewData(): array
    {
        if (! $this->hasSelectedSource()) {
            return ['nestedset' => collect()];
        }

        return parent::getViewData();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('standardSourceId')
                            ->label('Sumber Standar')
                            ->placeholder('— Pilih Sumber Standar —')
                            ->options(fn (): array => StandardSource::query()
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->live(debounce: 300)
                            ->columnSpanFull(),
                        Select::make('qualityPeriodId')
                            ->label('Periode Kualitas')
                            ->placeholder('— Semua Periode —')
                            ->options(fn (): array => QualityPeriod::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->live(debounce: 300)
                            ->visible(fn (): bool => $this->hasSelectedSource()),
                        Select::make('standardVersionId')
                            ->label('Versi Standar')
                            ->placeholder('— Semua Versi —')
                            ->options(function (): array {
                                return StandardVersion::query()
                                    ->with('standard')
                                    ->when(filled($this->qualityPeriodId), fn ($query) => $query->where('quality_period_id', (int) $this->qualityPeriodId))
                                    ->orderBy('version')
                                    ->get()
                                    ->mapWithKeys(fn (StandardVersion $version) => [
                                        $version->id => $version->version.' — '.$version->standard?->code,
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->live(debounce: 300)
                            ->visible(fn (): bool => $this->hasSelectedSource()),
                    ]),
            ]);
    }

    protected function infolistSchema(): array
    {
        return [];
    }

    public function getRecordLabel(Model $record): HtmlString|string
    {
        $code = e($record->code);
        $name = e($record->name);
        $isActive = (bool) $record->is_active;
        $statusLabel = $isActive ? 'AKTIF' : 'TIDAK AKTIF';
        $statusClasses = $isActive
            ? 'bg-success-50 text-success-700 ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/20'
            : 'bg-danger-50 text-danger-700 ring-danger-600/10 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/20';

        $indicatorCount = (int) ($record->indicators_count ?? 0);
        $indicatorBadgeHtml = '';

        if ($indicatorCount > 0) {
            $listUrl = e(IndicatorResource::getListUrl($record->getKey()));
            $indicatorBadgeHtml = <<<HTML
                <a href="{$listUrl}" class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-primary-50 text-primary-700 ring-primary-600/10 transition-colors hover:bg-primary-100 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/20 dark:hover:bg-primary-400/20">
                    {$indicatorCount} Indikator
                </a>
                HTML;
        }

        $descriptionHtml = filled($record->description)
            ? '<div class="fi-prose fi-in-text fi-size-sm mt-1 max-w-none text-gray-500 dark:text-gray-400">'
                .$record->renderRichContent('description')
                .'</div>'
            : '';

        return new HtmlString(<<<HTML
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span class="font-semibold text-gray-950 dark:text-white">{$code}</span>
                    <span class="text-gray-400"> </span>
                    <span class="text-gray-950 dark:text-white">{$name}</span>
                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {$statusClasses}">{$statusLabel}</span>
                    {$indicatorBadgeHtml}
                </div>
                {$descriptionHtml}
            </div>
            HTML);
    }
}
