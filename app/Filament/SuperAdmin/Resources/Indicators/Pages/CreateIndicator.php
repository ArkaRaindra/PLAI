<?php

namespace App\Filament\SuperAdmin\Resources\Indicators\Pages;

use App\Events\TraceabilityRecorded;
use App\Filament\SuperAdmin\Pages\ManageStandards;
use App\Filament\SuperAdmin\Resources\Indicators\IndicatorResource;
use App\Filament\SuperAdmin\Resources\StandarSources\StandarSourceResource;
use App\Models\Standard;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;

class CreateIndicator extends CreateRecord
{
    #[Url]
    public ?string $standardId = null;

    protected static string $resource = IndicatorResource::class;

    protected static ?string $title = 'Tambah Indikator';

    protected static ?string $breadcrumb = 'Tambah Indikator';

    protected static bool $canCreateAnother = false;

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
            'Tambah Indikator',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(IndicatorResource::getListUrl($this->standardId))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];
    }

    protected function fillForm(): void
    {
        $standard = Standard::query()->find($this->standardId);

        $this->form->fill([
            'standard_version_id' => $standard?->standardVersion?->id,
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $standard = Standard::query()->find($this->standardId);
        $data['standard_version_id'] = $standard?->standardVersion?->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return IndicatorResource::getListUrl($this->standardId);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(IndicatorResource::getListUrl($this->standardId));
    }

    protected function afterCreate(): void
    {
        $indicator = $this->record->loadMissing('standardVersion.standard', 'parentIndicator');
        $standard = $indicator->standardVersion?->standard;

        if ($standard !== null) {
            TraceabilityRecorded::dispatch(
                source: $standard,
                target: $indicator,
                relationType: 'defines',
            );
        }

        if ($indicator->parentIndicator !== null) {
            TraceabilityRecorded::dispatch(
                source: $indicator->parentIndicator,
                target: $indicator,
                relationType: 'related_to',
            );
        }
    }
}
