<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Pages;

use App\Events\TraceabilityRecorded;
use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use App\Filament\SuperAdmin\Resources\Targets\TargetResource;
use App\Models\Target;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;

class CreateRealization extends CreateRecord
{
    #[Url]
    public ?string $targetId = null;

    protected static string $resource = RealizationResource::class;

    protected static ?string $title = 'Tambah Realisasi';

    protected static ?string $breadcrumb = 'Tambah Realisasi';

    protected static bool $canCreateAnother = false;

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
            'Tambah Realisasi',
        ];
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->targetId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(RealizationResource::getListUrl($this->targetId))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'target_id' => (int) $this->targetId,
            'status' => 'draft',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['target_id'] = (int) $this->targetId;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return RealizationResource::getListUrl($this->targetId);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(RealizationResource::getListUrl($this->targetId));
    }

    protected function afterCreate(): void
    {
        $realization = $this->record->loadMissing('target');

        if ($realization->target === null) {
            return;
        }

        TraceabilityRecorded::dispatch(
            source: $realization,
            target: $realization->target,
            relationType: 'measured_by',
        );
    }
}
