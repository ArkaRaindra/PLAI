<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Models\IndicatorOwner;
use App\Models\QualityPeriod;
use App\Models\Realization;
use App\Models\Target;
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

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.super-admin.pages.target-achievement-monitoring';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                IndicatorOwner::query()
                    ->with([
                        'indicator.targets.realizations',
                        'organizationUnit',
                    ])
            )
            ->heading('Monitoring Capaian Target')
            ->description('Menampilkan seluruh pasangan indikator-unit yang wajib melapor pada periode terpilih')
            ->columns([
                TextColumn::make('indicator.name')
                    ->label('Indikator')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('organizationUnit.name')
                    ->label('Unit Organisasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('period_label')
                    ->label('Periode')
                    ->state(fn (): string => $this->currentPeriod()?->name ?? 'Pilih periode di atas'),
                TextColumn::make('target_value')
                    ->label('Target')
                    ->state(fn (IndicatorOwner $record): string => $this->targetFor($record)?->target_value !== null
                        ? number_format((float) $this->targetFor($record)->target_value, 2)
                        : '-'),
                TextColumn::make('actual_value')
                    ->label('Realisasi')
                    ->state(fn (IndicatorOwner $record): string => $this->realizationFor($record)?->actual_value !== null
                        ? number_format((float) $this->realizationFor($record)->actual_value, 2)
                        : '-'),
                TextColumn::make('achievement_percentage')
                    ->label('Achievement')
                    ->state(fn (IndicatorOwner $record): string => $this->achievementPercentage($record) !== null
                        ? number_format($this->achievementPercentage($record), 1).'%'
                        : '-')
                    ->badge()
                    ->color(fn (IndicatorOwner $record): string => $this->achievementColor($record)),
                TextColumn::make('progress_percentage')
                    ->label('Progress')
                    ->description(fn (IndicatorOwner $record): ?string => $this->progressDescription($record))
                    ->state(fn (IndicatorOwner $record): string => $this->progressPercentage() !== null
                        ? number_format($this->progressPercentage(), 1).'%'
                        : '-')
                    ->badge()
                    ->color(fn (IndicatorOwner $record): string => $this->progressColor($record)),
                TextColumn::make('overall_status')
                    ->label('Status')
                    ->state(fn (IndicatorOwner $record): string => $this->statusLabel($record))
                    ->badge()
                    ->color(fn (IndicatorOwner $record): string => $this->statusColor($record)),
            ])
            ->filters([
                SelectFilter::make('quality_period_id')
                    ->label('Periode')
                    ->options(fn () => QualityPeriod::query()->pluck('name', 'id'))
                    ->default(fn () => QualityPeriod::query()->where('is_active', true)->value('id'))
                    ->query(fn ($query, array $data) => $query->when(
                        $data['value'] ?? null,
                        fn ($q, $value) => $q->whereHas('indicator.targets', fn ($tq) => $tq->where('quality_period_id', $value)),
                    )),

                SelectFilter::make('organization_unit_id')
                    ->label('Unit Organisasi')
                    ->relationship('organizationUnit', 'name'),
            ])
            ->defaultSort('organization_unit_id');
    }

    private function currentPeriod(): ?QualityPeriod
    {
        $periodId = $this->getTableFilterState('quality_period_id')['value'] ?? null;

        if ($periodId) {
            return QualityPeriod::find($periodId);
        }

        return QualityPeriod::query()->where('is_active', true)->first();
    }

    private function targetFor(IndicatorOwner $record): ?Target
    {
        $period = $this->currentPeriod();

        if (! $period) {
            return null;
        }

        return $record->indicator->targets->firstWhere('quality_period_id', $period->id);
    }

    private function realizationFor(IndicatorOwner $record): ?Realization
    {
        $target = $this->targetFor($record);

        if (! $target) {
            return null;
        }

        return $target->realizations->firstWhere('organization_unit_id', $record->organization_unit_id);
    }

    private function achievementPercentage(IndicatorOwner $record): ?float
    {
        $target = $this->targetFor($record);
        $realization = $this->realizationFor($record);

        $targetValue = (float) ($target?->target_value ?? 0);

        if ($realization === null || $targetValue === 0) {
            return null;
        }

        return ((float) $realization->actual_value / $targetValue) * 100;
    }

    private function achievementColor(IndicatorOwner $record): string
    {
        if ($this->targetFor($record) === null) {
            return 'gray';
        }

        if ($this->realizationFor($record) === null) {
            return 'danger';
        }

        $achievement = $this->achievementPercentage($record);

        return match (true) {
            $achievement === null => 'gray',
            $achievement >= 100 => 'success',
            $achievement >= 75 => 'warning',
            default => 'danger',
        };
    }

    private function progressPercentage(): ?float
    {
        $period = $this->currentPeriod();

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

    private function progressColor(IndicatorOwner $record): string
    {
        $progress = $this->progressPercentage();

        if ($progress === null) {
            return 'gray';
        }

        if ($this->realizationFor($record) === null) {
            return $progress >= 50 ? 'danger' : 'warning';
        }

        $achievement = $this->achievementPercentage($record);

        if ($achievement === null) {
            return 'gray';
        }

        if ($this->realizationFor($record)?->status === 'approved' && $achievement >= 100) {
            return 'success';
        }

        return match (true) {
            $achievement >= $progress => 'success',
            $achievement >= $progress * 0.75 => 'warning',
            default => 'danger',
        };
    }

    private function progressDescription(IndicatorOwner $record): ?string
    {
        $progress = $this->progressPercentage();

        if ($progress === null) {
            return 'Periode tanpa tanggal mulai/selesai';
        }

        if ($this->realizationFor($record) === null) {
            return 'Belum ada realisasi';
        }

        $achievement = $this->achievementPercentage($record);

        if ($achievement === null) {
            return 'Waktu periode berjalan';
        }

        return $achievement >= $progress
            ? 'Sesuai atau lebih cepat dari jadwal periode'
            : 'Tertinggal dari jadwal periode';
    }

    private function statusLabel(IndicatorOwner $record): string
    {
        if ($this->targetFor($record) === null) {
            return 'Target Belum Ditetapkan';
        }

        $realization = $this->realizationFor($record);

        if ($realization === null) {
            return 'Belum Melapor';
        }

        if ($realization->status === 'rejected') {
            return 'Ditolak';
        }

        if ($realization->status !== 'approved') {
            return 'Dalam Proses';
        }

        $achievement = $this->achievementPercentage($record);

        return match (true) {
            $achievement === null => 'Disetujui',
            $achievement >= 100 => 'Tercapai',
            default => 'Belum Tercapai',
        };
    }

    private function statusColor(IndicatorOwner $record): string
    {
        if ($this->targetFor($record) === null) {
            return 'gray';
        }

        $realization = $this->realizationFor($record);

        if ($realization === null) {
            return 'danger';
        }

        if ($realization->status === 'rejected') {
            return 'danger';
        }

        if ($realization->status !== 'approved') {
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
