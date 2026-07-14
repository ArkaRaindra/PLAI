<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Pages;

use App\Filament\SuperAdmin\Resources\SelfAssessments\SelfAssessmentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditSelfAssessment extends EditRecord
{
    protected static string $resource = SelfAssessmentResource::class;

    protected static ?string $title = 'Ubah Self Assessment';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(fn (): string => self::$resource::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            ViewAction::make(),
            DeleteAction::make()->icon(Heroicon::Trash),
        ];
    }
}
