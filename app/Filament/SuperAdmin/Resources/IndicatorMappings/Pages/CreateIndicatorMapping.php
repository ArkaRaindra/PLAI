<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorMappings\Pages;

use App\Events\TraceabilityRecorded;
use App\Filament\SuperAdmin\Resources\IndicatorMappings\IndicatorMappingResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateIndicatorMapping extends CreateRecord
{
    protected static string $resource = IndicatorMappingResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];
    }

    protected function afterCreate(): void
    {
        $mapping = $this->record->loadMissing('internalIndicator', 'externalIndicator');

        if ($mapping->internalIndicator === null || $mapping->externalIndicator === null) {
            return;
        }

        TraceabilityRecorded::dispatch(
            source: $mapping->internalIndicator,
            target: $mapping->externalIndicator,
            relationType: 'mapped_to',
            metadata: [
                'is_primary' => $mapping->is_primary,
                'notes' => $mapping->notes,
            ],
        );
    }
}
