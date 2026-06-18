<?php

namespace App\Filament\SuperAdmin\Resources\StudyPrograms\Pages;

use App\Filament\SuperAdmin\Resources\StudyPrograms\StudyProgramResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Override;

class CreateStudyProgram extends CreateRecord
{
    protected static string $resource = StudyProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            CreateAction::make('save')
                ->label('Simpan')
                ->color('success'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    #[Override]
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
        ->visible(false);
    }

    #[Override]
    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->visible(false);
    }

    #[Override]
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->visible(false);
    }
}
