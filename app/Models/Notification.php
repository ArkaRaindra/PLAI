<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    use HasUuids;

    protected $table = 'notifications';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'message',
        'is_read',
        'type',
        'data',
        'action_url',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Notification $notification): void {
            $data = $notification->data ?? [];

            if (blank($notification->title)) {
                $notification->title = $data['title'] ?? 'Notifikasi';
            }

            if (blank($notification->message)) {
                $notification->message = $data['body'] ?? ($data['message'] ?? '');
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->forceFill([
                'is_read' => true,
                'read_at' => now(),
            ])->save();
        }
    }

    public function markAsUnread(): void
    {
        if ($this->is_read) {
            $this->forceFill([
                'is_read' => false,
                'read_at' => null,
            ])->save();
        }
    }
}
