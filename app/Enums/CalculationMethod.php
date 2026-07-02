<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CalculationMethod: string implements HasLabel
{
    case Count = 'count';
    case Sum = 'sum';
    case Avg = 'avg';
    case Min = 'min';
    case Max = 'max';
    case Percentage = 'percentage';
    case Ratio = 'ratio';
    case Boolean = 'boolean';
    case WeightedAverage = 'weighted_average';
    case TargetAchievement = 'target_achievement';
    case Score = 'score';
    case Index = 'index';
    case Trend = 'trend';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Count => 'Jumlah',
            self::Sum => 'Total',
            self::Avg => 'Rata-rata',
            self::Min => 'Nilai Minimum',
            self::Max => 'Nilai Maksimum',
            self::Percentage => 'Persentase',
            self::Ratio => 'Rasio',
            self::Boolean => 'Ya / Tidak',
            self::WeightedAverage => 'Rata-rata Tertimbang',
            self::TargetAchievement => 'Ketercapaian Target',
            self::Score => 'Skor',
            self::Index => 'Indeks',
            self::Trend => 'Tren',
        };
    }
}
