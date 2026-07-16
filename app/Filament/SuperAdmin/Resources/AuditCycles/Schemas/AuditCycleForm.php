<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Schemas;

use App\Models\AuditChecklistTemplate;
use App\Models\AuditCycle;
use App\Models\QualityPeriod;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class AuditCycleForm
{
    public const STATUS_LABELS = [
        'draft' => 'Draft',
        'ongoing' => 'Berjalan',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('quality_period_id')
                    ->label('Periode Audit')
                    ->options(
                        fn(?AuditCycle $record) => QualityPeriod::query()
                            ->where(function ($query) use ($record) {
                                $query->where('status', 'active');

                                if ($record?->quality_period_id) {
                                    $query->orWhere('id', $record->quality_period_id);
                                }
                            })
                            ->pluck('name', 'id'),
                    )
                    ->helperText('Hanya periode mutu berstatus aktif yang dapat dipilih.')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabledOn('edit'),

                Select::make('checklist_template_id')
                    ->label('Checklist Template')
                    ->options(
                        fn(?AuditCycle $record) => AuditChecklistTemplate::query()
                            ->active()
                            ->when(
                                $record?->checklist_template_id,
                                fn($query, $templateId) => $query->orWhere('id', $templateId)
                            )
                            ->get()
                            ->mapwithKeys(fn(AuditChecklistTemplate $template): array => [
                                $template->id => "{$template->name} (v{$template->version_no})",
                            ]),
                    )
                    ->helperText('Hanya versi terbaru dari tiap template yang dapat dipilih')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Select::make('status')
                    ->label('Status')
                    ->default('draft')
                    ->options(function (?AuditCycle $record): array {
                        if (! $record) {
                            return ['draft' => self::STATUS_LABELS['draft']];
                        }

                        $selectable = [$record->status, ...(AuditCycle::TRANSITIONS[$record->status] ?? [])];

                        return collect($selectable)
                            ->mapWithKeys(fn (string $value): array => [$value =>self::STATUS_LABELS[$value]])
                            ->all();
                    })
                    ->required()
                    ->disabledOn('create')
                    ->dehydrated(),
            ]);
    }
}
