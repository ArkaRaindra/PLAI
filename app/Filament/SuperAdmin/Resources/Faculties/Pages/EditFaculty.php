<?php

namespace App\Filament\SuperAdmin\Resources\Faculties\Pages;

use App\Filament\SuperAdmin\Resources\Faculties\FacultyResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditFaculty extends EditRecord
{
    protected static string $resource = FacultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return FacultyResource::getUrl('index');
    }
}
