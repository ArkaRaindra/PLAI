<?php

namespace App\Models;

use App\Blameable;
use App\Events\TraceabilityRecorded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SelfAssessmentDetail extends Model
{
    use Blameable;

    protected $table = 'self_assessment_details';

    protected $fillable = [
        'self_assessment_id',
        'realization_id',
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

        static::saved(function (SelfAssessmentDetail $detail) use ($syncParentFinalScore): void {
            $detail->recordEvaluationTraceability();
            $syncParentFinalScore($detail);
        });
        static::deleted($syncParentFinalScore);
    }

    public function recordEvaluationTraceability(): void
    {
        if (! Auth::check()) {
            return;
        }

        $this->loadMissing('realization', 'selfAssessment');

        if ($this->realization === null || $this->selfAssessment === null) {
            return;
        }

        $measuredByPerformedAt = TraceabilityLinks::query()
            ->where('source_type', 'realization')
            ->where('source_id', $this->realization_id)
            ->where('relation_type', 'measured_by')
            ->value('performed_at');

        TraceabilityRecorded::dispatch(
            source: $this->realization,
            target: $this->selfAssessment,
            relationType: 'evaluated_in',
            performedAt: $measuredByPerformedAt
                ? Carbon::parse($measuredByPerformedAt)->addSecond()
                : now(),
        );
    }

    public function selfAssessment(): BelongsTo
    {
        return $this->belongsTo(SelfAssessment::class);
    }

    public function realization(): BelongsTo
    {
        return $this->belongsTo(Realization::class);
    }
}
