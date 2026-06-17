<?php

namespace App\Filament\Prodi\Resources\AuditScores\Pages;

use App\Filament\Prodi\Resources\AuditScores\AuditScoreResource;
use App\Filament\Prodi\Widgets\AchievementScorePerProdiWidget;
use App\Filament\Prodi\Widgets\RadarScoreWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

class ListAuditScores extends ListRecords
{
    protected static string $resource = AuditScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return[
            AchievementScorePerProdiWidget::class,
            RadarScoreWidget::class,
        ];
    }
}
