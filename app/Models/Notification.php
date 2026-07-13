<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is-read',
        'action_url'
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    protected function user(): BelongsTo
    {
        return $this->belongsto(User::class);
    }

    protected function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update(['is_read' => true]);
        }
    }
}
