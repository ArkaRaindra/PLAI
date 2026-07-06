<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Pages;

use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use App\Filament\SuperAdmin\Resources\Targets\TargetResource;
use App\Models\Target;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewRealization extends ViewRecord
{
    protected static string $resource = RealizationResource::class;

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
            'Detail Realisasi',
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
            EditAction::make()
                ->visible(fn ($record) => ! in_array($record->status, [
                    'submitted',
                    'approved'
                ])),
            ...RealizationResource::workflowActions(),
        ];
    }

    protected function getTargetLabel(Target $target): string
    {
        $target->loadMissing(['indicator:id,name', 'qualityPeriod:id,code']);

        return trim(($target->indicator?->name ?? '').' — '.($target->qualityPeriod?->code ?? ''));
    }
}
