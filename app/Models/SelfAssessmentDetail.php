<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelfAssessmentDetail extends Model
{
    use Blameable;

    protected $table = 'self_assessment_details';

    protected $fillable = [
        'self_assessment_id',
        'indicator_id',
        'score',
        'analysis',
        'strength',
        'weakness',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        $syncParentFinalScore = function (SelfAssessmentDetail $detail): void {
            $selfAssessment = $detail->selfAssessment;

            if ($selfAssessment === null) {
                return;
            }

            $selfAssessment->recalculateFinalScore();
            $selfAssessment->saveQuietly();
        };

        static::saved($syncParentFinalScore);
        static::deleted($syncParentFinalScore);
    }

    public function selfAssessment(): BelongsTo
    {
        return $this->belongsTo(SelfAssessment::class);
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

}
