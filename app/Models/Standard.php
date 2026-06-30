<?php

namespace App\Models;

use App\Blameable;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;

class Standard extends Model implements HasRichContent
{
    use Blameable, HasFactory, InteractsWithRichContent, NodeTrait;

    protected $fillable = [
        'code', 'name', 'description', 'standard_source_id', 'is_active',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getScopeAttributes(): array
    {
        return ['standard_source_id'];
    }

    public function standardSource(): BelongsTo
    {
        return $this->belongsTo(StandardSource::class, 'standard_source_id');
    }

    protected function setUpRichContent(): void
    {
        $this->registerRichContent('description');
    }
}
