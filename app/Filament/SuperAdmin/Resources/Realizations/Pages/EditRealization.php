<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Pages;

use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use App\Filament\SuperAdmin\Resources\Targets\TargetResource;
use App\Models\Target;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditRealization extends EditRecord
{
    protected static string $resource = RealizationResource::class;

    protected static ?string $title = 'Ubah Realisasi';

    protected static ?string $breadcrumb = 'Ubah Realisasi';

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        $target = $this->record->target;

        if ($target === null) {
            return parent::getBreadcrumbs();
        }

        $targetLabel = $this->getTargetLabel($target);

        return [
            TargetResource::getUrl('index') => TargetResource::getNavigationLabel(),
            RealizationResource::getListUrl($target->id) => $targetLabel,
            'Ubah Realisasi',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(RealizationResource::getListUrl($this->record->target_id))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            ViewAction::make(),
            DeleteAction::make()
                ->icon(Heroicon::Trash)
                ->authorize(fn (): bool => auth()->user()?->can('delete', $this->record) ?? false)
                ->successRedirectUrl(RealizationResource::getListUrl($this->record->target_id)),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['target_id'] = $this->record->target_id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return RealizationResource::getUrl('view', ['record' => $this->record]);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(RealizationResource::getUrl('view', ['record' => $this->record]));
    }

    protected function getTargetLabel(Target $target): string
    {
        $target->loadMissing(['indicator:id,name', 'qualityPeriod:id,code']);

        return trim(($target->indicator?->name ?? '').' — '.($target->qualityPeriod?->code ?? ''));
    }
}
