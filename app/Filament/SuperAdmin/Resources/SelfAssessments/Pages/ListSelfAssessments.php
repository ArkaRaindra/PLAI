<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Pages;

use App\Filament\SuperAdmin\Resources\SelfAssessments\SelfAssessmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListSelfAssessments extends ListRecords
{
    protected static string $resource = SelfAssessmentResource::class;

    protected static ?string $title = 'Self Assessment';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Self Assessment')
                ->icon(Heroicon::Plus),
        ];
    }
}
