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
                    ->description(fn (Realization $record): ?string => $this->progressDescription($record))
                    ->state(fn (Realization $record): string => $this->progressPercentage($record) !== null
                        ? number_format($this->progressPercentage($record), 1).'%'
                        : '-')
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

    private function progressPercentage(Realization $record): ?float
    {
        $period = $record->target?->qualityPeriod;

        if (! $period?->start_date || ! $period?->end_date) {
            return null;
        }

        $today = now()->startOfDay();
        $start = $period->start_date->copy()->startOfDay();
        $end = $period->end_date->copy()->startOfDay();

        if ($today->lessThanOrEqualTo($start)) {
            return 0.0;
        }
        if ($today->greaterThanOrEqualTo($end)) {
            return 100.0;
        }

        $totalDays = $start->diffInDays($end);
        $elapsedDays = $start->diffInDays($today);

        return round(($elapsedDays / $totalDays) * 100, 1);
    }

    private function progressColor(Realization $record): string
    {
        $progress = $this->progressPercentage($record);
        $achievement = $this->achievementPercentage($record);

        if ($progress === null) {
            return 'gray';
        }

        if ($achievement === null) {
            return 'gray';
        }

        if ($record->status === 'approved' && $achievement >= 100) {
            return 'success';
        }

        return match (true) {
            $achievement >= $progress => 'success',
            $achievement >= $progress * 0.75 => 'warning',
            default => 'danger',
        };
    }

    private function progressDescription(Realization $record): ?string
    {
        $progress = $this->progressPercentage($record);
        $achievement = $this->achievementPercentage($record);

        if ($progress === null) {
            return 'Periode tanpa tanggal mulai/selesai';
        }

        if ($achievement === null) {
            return $progress >= 100 ? 'Periode berakhir (tidak ada target)' : 'Periode berjalan (tidak ada target)';
        }

        return $achievement >= $progress
            ? 'Sesuai atau lebih cepat dari jadwal periode'
            : 'Tertinggal dari jadwal periode';
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
            $achievement >= 75 => 'warning',
            default => 'danger',
        };
    }
}
