<?php

namespace App\Filament\SuperAdmin\Resources\Standards\Pages;

use App\Filament\SuperAdmin\Resources\Standards\StandardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class EditStandard extends EditRecord
{
    protected static string $resource = StandardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return StandardResource::getUrl('index');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Edit ' . $this->record->code;
    }
}
