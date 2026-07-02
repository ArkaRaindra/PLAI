<?php

namespace App;

use Illuminate\Support\Facades\Auth;

trait Blameable
{
    protected static function bootBlameable(): void
    {
        static::creating(function ($model) {
            if ($model->isDirty('created_by')) {
                return;
            }

            if (Auth::check()) {
                $model->created_by = Auth::id();
            } else {
                $model->created_by = 'system';
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('updated_by')) {
                return;
            }

            if (Auth::check()) {
                $model->updated_by = Auth::id();
            } else {
                $model->updated_by = 'system';
            }
        });
    }
}
