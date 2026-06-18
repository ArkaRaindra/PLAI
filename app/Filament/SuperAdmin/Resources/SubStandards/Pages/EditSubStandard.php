<?php

namespace App\Filament\SuperAdmin\Resources\SubStandards\Pages;

use App\Filament\SuperAdmin\Resources\SubStandards\SubStandardResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EditSubStandard extends EditRecord
{
    protected static string $resource = SubStandardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            Action::make('save')
                ->label('Simpan Perubahan')
                ->action(fn () => $this->save())
                ->color('success'),
            DeleteAction::make()
                ->label('Hapus'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Edit '.$this->record->code;
    }
}
