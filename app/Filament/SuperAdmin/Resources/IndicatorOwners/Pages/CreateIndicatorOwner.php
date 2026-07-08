<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorOwners\Pages;

use App\Events\TraceabilityRecorded;
use App\Filament\SuperAdmin\Resources\IndicatorOwners\IndicatorOwnerResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateIndicatorOwner extends CreateRecord
{
    protected static string $resource = IndicatorOwnerResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(IndicatorOwnerResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function afterCreate(): void
    {
        $owner = $this->record->loadMissing('indicator', 'organizationUnit');

        if ($owner->indicator === null || $owner->organizationUnit === null) {
            return;
        }

        TraceabilityRecorded::dispatch(
            source: $owner->indicator,
            target: $owner->organizationUnit,
            relationType: 'supported_by',
            metadata: [
                'is_primary' => $owner->is_primary,
                'user_position_id' => $owner->user_position_id,
                'notes' => $owner->notes,
            ],
        );
    }
}
