<?php

namespace App\Filament\SuperAdmin\Resources\Positions\Pages;

use App\Filament\SuperAdmin\Resources\Positions\PositionResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreatePosition extends CreateRecord
{
    protected static string $resource = PositionResource::class;

    protected static ?string $title = 'Tambah Jabatan';

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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id() ?? User::first()?->id;

        return $data;
    }

    // protected function getCreateFormAction(): Action
    // {
    //     return parent::getCreateFormAction()
    //         ->visible(false);
    // }

    // protected function getCreateAnotherFormAction(): Action
    // {
    //     return parent::getCreateAnotherFormAction()
    //         ->visible(false);
    // }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->visible(false);
    }
}
