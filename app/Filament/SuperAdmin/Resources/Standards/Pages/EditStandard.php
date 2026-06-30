<?php

namespace App\Filament\SuperAdmin\Resources\Standards\Pages;

use App\Filament\SuperAdmin\Pages\ManageStandards;
use App\Filament\SuperAdmin\Resources\Standards\StandardResource;
use App\Filament\SuperAdmin\Resources\StandarSources\StandarSourceResource;
use App\Models\StandardSource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditStandard extends EditRecord
{
    protected static string $resource = StandardResource::class;

    protected static ?string $title = 'Ubah Standar';

    protected static ?string $breadcrumb = 'Ubah Standar';

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [
            StandarSourceResource::getUrl() => StandarSourceResource::getNavigationLabel(),
        ];

        $source = StandardSource::query()->find($this->record->standard_source_id);

        if ($source !== null) {
            $breadcrumbs[ManageStandards::getUrl(['standardSourceId' => $source->id])] = $source->name;
        }

        return [
            ...$breadcrumbs,
            'Ubah Standar',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(StandardResource::getManageStandardsUrl($this->record->standard_source_id))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            DeleteAction::make()->icon(Heroicon::Trash),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return StandardResource::getManageStandardsUrl($this->record->standard_source_id);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(StandardResource::getManageStandardsUrl($this->record->standard_source_id));
    }
}
