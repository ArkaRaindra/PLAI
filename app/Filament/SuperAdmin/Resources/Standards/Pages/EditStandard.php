<?php

namespace App\Filament\SuperAdmin\Resources\Standards\Pages;

use App\Filament\SuperAdmin\Pages\ManageStandards;
use App\Filament\SuperAdmin\Resources\Standards\StandardResource;
use App\Filament\SuperAdmin\Resources\StandarSources\StandarSourceResource;
use App\Models\StandardSource;
use App\Support\StandardVersionPersister;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing('standardVersion');

        return array_merge($data, StandardVersionPersister::toFormData($this->record->standardVersion));
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            $versionData = StandardVersionPersister::stripNestedFormData($data);

            $record->update($data);
            $record->refresh();
            StandardVersionPersister::sync($record, $versionData);

            return $record;
        });
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
