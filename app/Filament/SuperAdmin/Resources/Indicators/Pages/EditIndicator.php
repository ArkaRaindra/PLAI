<?php

namespace App\Filament\SuperAdmin\Resources\Indicators\Pages;

use App\Filament\SuperAdmin\Pages\ManageStandards;
use App\Filament\SuperAdmin\Resources\Indicators\IndicatorResource;
use App\Filament\SuperAdmin\Resources\StandarSources\StandarSourceResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditIndicator extends EditRecord
{
    protected static string $resource = IndicatorResource::class;

    protected static ?string $title = 'Ubah Indikator';

    protected static ?string $breadcrumb = 'Ubah Indikator';

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        $standard = $this->record->standard;

        $breadcrumbs = [
            StandarSourceResource::getUrl() => StandarSourceResource::getNavigationLabel(),
            ManageStandards::getUrl(['standardSourceId' => $standard->standard_source_id]) => $standard->standardSource?->name ?? 'Standar',
            IndicatorResource::getListUrl($standard->id) => $standard->code.' '.$standard->name,
        ];

        return [
            ...$breadcrumbs,
            'Ubah Indikator',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(IndicatorResource::getListUrl($this->record->standard_id))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            DeleteAction::make()->icon(Heroicon::Trash),
        ];
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        $this->form->fill([
            'standard_id' => $this->record->standard_id,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return IndicatorResource::getListUrl($this->record->standard_id);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(IndicatorResource::getListUrl($this->record->standard_id));
    }
}
