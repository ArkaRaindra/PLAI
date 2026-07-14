<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages\ListEvidenceReviews;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages\ViewEvidenceReview;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\Schemas\EvidenceReviewInfolist;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\Tables\EvidenceReviewsTable;
use App\Models\EvidenceReview;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class EvidenceReviewResource extends Resource
{
    protected static ?string $model = EvidenceReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Review Evidence';

    protected static ?string $pluralModelLabel = 'Review Evidence';

    protected static ?string $slug = 'evidence-reviews';

    // protected static string|UnitEnum|null $navigationGroup = 'Evidence';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->can('evidence.review') ?? false;
    }

    public static function table(Table $table): Table
    {
        return EvidenceReviewsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EvidenceReviewInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvidenceReviews::route('/'),
            'view' => ViewEvidenceReview::route('/{record}'),
        ];
    }

    public static function getListUrl(): string
    {
        return static::getUrl('index');
    }
}
