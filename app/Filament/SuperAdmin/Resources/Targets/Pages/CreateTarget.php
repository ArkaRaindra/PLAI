<?php

namespace App\Filament\SuperAdmin\Resources\Targets\Pages;

use App\Events\TraceabilityRecorded;
use App\Filament\SuperAdmin\Resources\Targets\TargetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTarget extends CreateRecord
{
    protected static string $resource = TargetResource::class;

    protected function afterCreate(): void
    {
        $target = $this->record->loadMissing('indicator');

        if ($target->indicator === null) {
            return;
        }

        TraceabilityRecorded::dispatch(
            source: $target->indicator,
            target: $target,
            relationType: 'defines',
        );
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
