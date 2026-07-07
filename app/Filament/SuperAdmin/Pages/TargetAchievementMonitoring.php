<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Models\QualityPeriod;
use App\Models\Realization;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TargetAchievementMonitoring extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $routePath = 'target-achievement-monitoring';

    protected static ?string $navigationLabel = 'Monitoring Capaian Target';

    protected static ?string $title = 'Monitoring Capaian Target';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static string|\UnitEnum|null $navigationGroup = 'Indikator';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.super-admin.pages.target-achievement-monitoring';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Realization::query()->with(['target.indicator', 'target.qualityPeriod', 'organizationUnit'])
            )
            ->heading('Monitoring Capaian Target')
            ->description('Achievement, Progress pelaporan, dan Status setiap realisasi target')
            ->columns([
                TextColumn::make('target.indicator.name')
                    ->label('Indikator')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('organizationUnit.name')
                    ->label('Unit Organisasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('target.qualityPeriod.code')
                    ->label('Periode')
                    ->sortable(),
                TextColumn::make('target.target_value')
                    ->label('Target')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('actual_value')
                    ->label('Realisasi')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('achievement_percentage')
                    ->label('Achievement')
                    ->state(fn (Realization $record): string => $this->achievementPercentage($record) !== null
                        ? number_format($this->achievementPercentage($record), 1).'%'
                        : '-')
                    ->badge()
                    ->color(fn (Realization $record): string => $this->achievementColor($this->achievementPercentage($record))),
                TextColumn::make('progress_percentage')
                    ->label('Progress')
                    ->state(fn (Realization $record): string => $this->progressPercentage($record).'%')
                    ->badge()
                    ->color(fn (Realization $record): string => $this->progressColor($record)),

                TextColumn::make('overall_status')
                    ->label('Status')
                    ->state(fn (Realization $record): string => $this->statusLabel($record))
                    ->badge()
                    ->color(fn (Realization $record): string => $this->statusColor($record)),
            ])
            ->filters([
                SelectFilter::make('quality_period_id')
                    ->label('Periode')
                    ->options(fn () => QualityPeriod::query()->pluck('name', 'id'))
                    ->query(fn ($query, array $data) => $query->when(
                        $data['value'] ?? null,
                        fn ($q, $value) => $q->whereHas('target', fn ($tq) => $tq->where('quality_period_id', $value)),
                    )),

                SelectFilter::make('organization_unit_id')
                    ->label('Unit Organisasi')
                    ->relationship('organizationUnit', 'name'),

                SelectFilter::make('status')
                    ->label('Status Realisasi')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Diajukan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private function achievementPercentage(Realization $record): ?float
    {
        $targetValue = (float) ($record->target->target_value ?? 0);

        if ($targetValue === 0) {
            return null;
        }

        return ((float) $record->actual_value / $targetValue) * 100;
    }

    private function achievementColor(?float $achievement): string
    {
        return match (true) {
            $achievement === null => 'gray',
            $achievement >= 100 => 'success',
            $achievement >= 75 => 'warning',
            default => 'danger',
        };
    }

    private function progressPercentage(Realization $record): int
    {
        return match ($record->status) {
            'approved' => 100,
            'submitted' => 60,
            'rejected' => 40,
            default => 25,
        };
    }

    private function progressColor(Realization $record): string
    {
        return match ($record->status) {
            'approved' => 'success',
            'submitted' => 'info',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    private function statusLabel(Realization $record): string
    {
        if ($record->status === 'rejected') {
            return 'Ditolak';
        }

        if ($record->status !== 'approved') {
            return 'Dalam Proses';
        }

        $achievement = $this->achievementPercentage($record);

        return match (true) {
            $achievement === null => 'Disetujui',
            $achievement >= 100 => 'Tercapai',
            default => 'Belum Tercapai',
        };
    }

    private function statusColor(Realization $record): string
    {
        if ($record->status === 'rejected') {
            return 'danger';
        }

        if ($record->status !== 'approved') {
            return 'info';
        }

        $achievement = $this->achievementPercentage($record);

        return match (true) {
            $achievement === null => 'gray',
            $achievement >= 100 => 'success',
            default => 'warning',
        };
    }
}
