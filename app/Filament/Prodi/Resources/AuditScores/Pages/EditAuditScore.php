<?php

namespace App\Filament\Prodi\Resources\AuditScores\Pages;

use App\Filament\Prodi\Resources\AuditScores\AuditScoreResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAuditScore extends EditRecord
{
    protected static string $resource = AuditScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
