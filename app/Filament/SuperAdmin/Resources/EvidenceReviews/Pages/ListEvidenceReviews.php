<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use App\Models\EvidenceReview;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListEvidenceReviews extends ListRecords
{
    protected static string $resource = EvidenceReviewResource::class;

    protected static ?string $title = 'Review Evidence';

    protected static ?string $breadcrumb = 'Review Evidence';

    protected function getTableQuery(): Builder
    {
        return EvidenceReview::query()->pending();
    }
}
