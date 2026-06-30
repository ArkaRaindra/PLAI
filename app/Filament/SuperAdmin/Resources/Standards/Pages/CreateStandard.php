<?php

namespace App\Filament\SuperAdmin\Resources\Standards\Pages;

use App\Filament\SuperAdmin\Pages\ManageStandards;
use App\Filament\SuperAdmin\Resources\Standards\StandardResource;
use App\Filament\SuperAdmin\Resources\StandarSources\StandarSourceResource;
use App\Models\Standard;
use App\Models\StandardSource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;

class CreateStandard extends CreateRecord
{
    #[Url]
    public ?string $standardSourceId = null;

    #[Url]
    public ?string $parentId = null;

    protected static string $resource = StandardResource::class;

    protected static ?string $title = 'Tambah Standar';

    protected static ?string $breadcrumb = 'Tambah Standar';

    protected static bool $canCreateAnother = false;

    public function mount(): void
    {
        if (blank($this->standardSourceId)) {
            $this->redirect(StandardResource::getManageStandardsUrl());

            return;
        }

        parent::mount();
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [
            StandarSourceResource::getUrl() => StandarSourceResource::getNavigationLabel(),
        ];

        $source = StandardSource::query()->find($this->standardSourceId);

        if ($source !== null) {
            $breadcrumbs[ManageStandards::getUrl(['standardSourceId' => $this->standardSourceId])] = $source->name;
        }

        return [
            ...$breadcrumbs,
            'Tambah Standar',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(StandardResource::getManageStandardsUrl($this->standardSourceId))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'standard_source_id' => (int) $this->standardSourceId,
            'parent_id' => filled($this->parentId) ? (int) $this->parentId : null,
            'is_active' => true,
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $parentId = $data['parent_id'] ?? null;
        unset($data['parent_id']);

        $parent = filled($parentId)
            ? Standard::scoped(['standard_source_id' => $data['standard_source_id']])->findOrFail($parentId)
            : null;

        return Standard::create($data, $parent);
    }

    protected function getRedirectUrl(): string
    {
        return StandardResource::getManageStandardsUrl($this->standardSourceId);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(StandardResource::getManageStandardsUrl($this->standardSourceId));
    }
}
