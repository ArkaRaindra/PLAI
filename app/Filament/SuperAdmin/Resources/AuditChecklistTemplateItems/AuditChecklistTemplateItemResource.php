<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Pages\CreateAuditChecklistTemplateItem;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Pages\EditAuditChecklistTemplateItem;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Pages\ListAuditChecklistTemplateItems;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Pages\ViewAuditChecklistTemplateItem;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Schemas\AuditChecklistTemplateItemForm;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Schemas\AuditChecklistTemplateItemInfolist;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Tables\AuditChecklistTemplateItemsTable;
use App\Models\AuditChecklistTemplateItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AuditChecklistTemplateItemResource extends Resource
{
    protected static ?string $model = AuditChecklistTemplateItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static ?string $recordTitleAttribute = 'question';

    protected static ?string $modelLabel = 'Item Checklist';

    protected static ?string $pluralModelLabel = 'Item Checklist';

    protected static ?string $slug = 'audit-checklist-template-items';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return AuditChecklistTemplateItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditChecklistTemplateItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditChecklistTemplateItemsTable::configure($table);
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
            'index' => ListAuditChecklistTemplateItems::route('/'),
            'create' => CreateAuditChecklistTemplateItem::route('/create'),
            'view' => ViewAuditChecklistTemplateItem::route('/{record}'),
            'edit' => EditAuditChecklistTemplateItem::route('/{record}/edit'),
        ];
    }

    public static function getListUrl(int|string $templateId): string
    {
        return static::getUrl('index').'?'.http_build_query([
            'templateid' => $templateId,
        ]);
    }

    public static function getCreateUrl (int|string $templateId): string
    {
        return static::getUrl('create').'?'.http_build_query([
            'templateId' => $templateId,
        ]);
    }
}
