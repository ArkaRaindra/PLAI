<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Schemas;

use App\Models\AuditChecklistTemplate;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

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
                    ->options(function (): array {
                        $latest = AuditChecklistTemplate::query()
                            ->select('name')
                            ->selectRaw('MAX(CAST(version_no AS DECIMAL(10,1))) as max_version')
                            ->groupBy('name')
                            ->get()
                            ->mapWithKeys(fn ($row): array => ["{$row->name}|{$row->max_version}" => true]);

                        return AuditChecklistTemplate::query()
                            ->whereIn(DB::raw("CONCAT(name, '|', CAST(version_no AS DECIMAL(10,1)))"), $latest->keys()->values()->all())
                            ->orderBy('name')
                            ->orderByDesc(DB::raw('CAST(version_no AS DECIMAL(10,1))'))
                            ->get()
                            ->mapWithKeys(fn (AuditChecklistTemplate $template): array => [
                                $template->id => "{$template->name} (v{$template->version_no})",
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
