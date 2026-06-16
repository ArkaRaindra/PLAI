<?php

namespace App\Filament\Prodi\Resources\AuditScores\Pages;

use App\Filament\Prodi\Resources\AuditScores\AuditScoreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAuditScores extends ListRecords
{
    protected static string $resource = AuditScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
