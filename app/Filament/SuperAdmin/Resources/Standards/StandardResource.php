<?php

namespace App\Filament\SuperAdmin\Resources\Standards;

use App\Filament\SuperAdmin\Pages\ManageStandards;
use App\Filament\SuperAdmin\Resources\Standards\Pages\CreateStandard;
use App\Filament\SuperAdmin\Resources\Standards\Pages\EditStandard;
use App\Filament\SuperAdmin\Resources\Standards\Schemas\StandardForm;
use App\Models\Standard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use LaraZeus\Tabler\Tabler;

class StandardResource extends Resource
{
    protected static ?string $model = Standard::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::ListTree;

    protected static ?string $modelLabel = 'Standar';

    protected static ?string $pluralModelLabel = 'Standar';

    protected static ?string $slug = 'standards';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return StandardForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateStandard::route('/create'),
            'edit' => EditStandard::route('/{record}/edit'),
        ];
    }

    public static function getCreateUrl(int|string $standardSourceId, int|string|null $parentId = null): string
    {
        $query = http_build_query(array_filter([
            'standardSourceId' => $standardSourceId,
            'parentId' => $parentId,
        ]));

        return static::getUrl('create').'?'.$query;
    }

    public static function getManageStandardsUrl(int|string|null $standardSourceId = null): string
    {
        if (blank($standardSourceId)) {
            return ManageStandards::getUrl();
        }

        return ManageStandards::getUrl(['standardSourceId' => $standardSourceId]);
    }
}
