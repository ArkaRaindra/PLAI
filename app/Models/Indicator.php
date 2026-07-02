<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indicator extends Model
{
    use Blameable, HasFactory;

    protected $fillable = [
        'standard_id', 'parent_indicator_id', 'code', 'name', 'description', 'calculation_method', 'measurement_unit', 'weight',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
        ];
    }

    public const COUNT = 'count';

    public const SUM = 'sum';

    public const AVG = 'avg';

    public const MIN = 'min';

    public const MAX = 'max';

    public const PERCENTAGE = 'percentage';

    public const RATIO = 'ratio';

    public const BOOLEAN = 'boolean';

    public const WEIGHTED_AVERAGE = 'weighted_average';

    public const TARGET_ACHIEVEMENT = 'target_achievement';

    public const SCORE = 'score';

    public const INDEX = 'index';

    public const TREND = 'trend';

    public static function options(): array
    {
        return [
            self::COUNT => 'Jumlah',
            self::SUM => 'Total',
            self::AVG => 'Rata-rata',
            self::MIN => 'Nilai Minimum',
            self::MAX => 'Nilai Maksimum',
            self::PERCENTAGE => 'Persentase',
            self::RATIO => 'Rasio',
            self::BOOLEAN => 'Ya / Tidak',
            self::WEIGHTED_AVERAGE => 'Rata-rata Tertimbang',
            self::TARGET_ACHIEVEMENT => 'Ketercapaian Target',
            self::SCORE => 'Skor',
            self::INDEX => 'Indeks',
            self::TREND => 'Tren',
        ];
    }

    public function parentIndicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class, 'parent_indicator_id');
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function indicatorOwners(): HasMany
    {
        return $this->hasMany(IndicatorOwner::class);
    }

    public function indicatorMappingsInternal(): HasMany
    {
        return $this->hasMany(IndicatorMapping::class, 'internal_indicator_id');
    }

    public function indicatorMappingsExternal(): HasMany
    {
        return $this->hasMany(IndicatorMapping::class, 'external_indicator_id');
    }

    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }   
}
