<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriods\Pages;

use App\Filament\SuperAdmin\Resources\QualityPeriods\QualityPeriodResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditQualityPeriod extends EditRecord
{
    protected static string $resource = QualityPeriodResource::class;

    protected static ?string $title = 'Edit Periode Kualitas';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            ViewAction::make()->icon(Heroicon::Eye)->color('info'),
            DeleteAction::make()->icon(Heroicon::Trash),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id() ?? User::first()?->id;

        return $data;
    }
}
