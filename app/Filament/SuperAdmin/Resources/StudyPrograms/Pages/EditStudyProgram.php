<?php

namespace App\Filament\SuperAdmin\Resources\StudyPrograms\Pages;

use App\Filament\SuperAdmin\Resources\StudyPrograms\StudyProgramResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Override;

class EditStudyProgram extends EditRecord
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
        return StudyProgramResource::getUrl('index');
    }

    #[Override]
    protected function getFormActions(): array
    {
        return [];
    }

}
