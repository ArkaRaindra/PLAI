<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;

class CorrectiveActionUpdate extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'corrective_action_id',
        'progress_percentage',
        'description',
        'updated_by',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
            'updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CorrectiveActionUpdate $update): void {
            if (blank($update->updated_at)) {
                $update->updated_at = now();
            }

            if (blank($update->updated_by) && Auth::check()) {
                $update->updated_by = Auth::id();
            }

            if ($update->progress_percentage < 0 || $update->progress_percentage > 100) {
                throw new \RuntimeException('Progress percentage harus di antara 0 dan 100.');
            }

            $latest = self::query()
                ->where('corrective_action_id', $update->corrective_action_id)
                ->orderByDesc('id')
                ->value('progress_percentage');
            
            if ($latest !== null && $update->progress_percentage < $latest) {
                throw new \RuntimeException("Progress tidak dapat dikurangi (progress terakhir: {$latest}%).");
            }
        });
        static::updating(function (): void {
            throw new \RuntimeException('Progress update bersifat immutable dan tidak dapat diubah.');
        });

        static::deleting(function (): void {
            throw new \RuntimeException('Progress update bersifat immutable dan tidak dapat dihapus.');
        });
    }

    public function correctiveAction(): BelongsTo
    {
        return $this->belongsTo(CorrectiveAction::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function evidenceLink(): MorphOne
    {
        return $this->morphOne(EvidenceLinks::class, 'reference');
    }

    public function evidence(): ?Evidences
    {
        return $this->evidenceLink?->evidence;
    }
}
