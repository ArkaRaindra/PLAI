<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceLinks;

use App\Filament\SuperAdmin\Resources\EvidenceLinks\Pages\CreateEvidenceLink;
use App\Filament\SuperAdmin\Resources\EvidenceLinks\Pages\EditEvidenceLink;
use App\Filament\SuperAdmin\Resources\EvidenceLinks\Pages\ListEvidenceLinks;
use App\Filament\SuperAdmin\Resources\EvidenceLinks\Schemas\EvidenceLinkForm;
use App\Filament\SuperAdmin\Resources\EvidenceLinks\Tables\EvidenceLinksTable;
use App\Models\EvidenceLinks;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EvidenceLinkResource extends Resource
{
    protected static ?string $model = EvidenceLinks::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Evidence Link';

    protected static ?string $pluralModelLabel = 'Evidence Link';

    protected static ?string $slug = 'evidence-links';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return EvidenceLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvidenceLinksTable::configure($table);
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
            'index' => ListEvidenceLinks::route('/'),
            'create' => CreateEvidenceLink::route('/create'),
            'edit' => EditEvidenceLink::route('/{record}/edit'),
        ];
    }

    public static function getListUrl(int|string $evidenceId): string
    {
        return static::getUrl('index').'?'.http_build_query([
            'evidenceId' => $evidenceId,
        ]);
    }

    public static function getCreateUrl(int|string $evidenceId): string
    {
        return static::getUrl('create').'?'.http_build_query([
            'evidenceId' => $evidenceId,
        ]);
    }
}
