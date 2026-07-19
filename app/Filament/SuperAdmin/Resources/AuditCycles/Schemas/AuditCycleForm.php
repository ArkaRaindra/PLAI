<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Schemas;

use App\Models\AuditChecklistTemplate;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class AuditCycleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('quality_period_id')
                    ->label('Periode')
                    ->relationship(name: 'qualityPeriod', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('checklist_template_id')
                    ->label('Checklist Template')
                    ->options(fn () => AuditChecklistTemplate::query()
                        ->get()
                        ->mapWithKeys(fn (AuditChecklistTemplate $template): array => [
                            $template->id => "{$template->name} (v{$template->version_no})",
                        ]))
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
