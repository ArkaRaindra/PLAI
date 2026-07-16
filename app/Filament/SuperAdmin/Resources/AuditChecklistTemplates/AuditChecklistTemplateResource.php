<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages\CreateAuditChecklistTemplate;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages\EditAuditChecklistTemplate;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages\ListAuditChecklistTemplates;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages\ViewAuditChecklistTemplate;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\RelationManagers\ItemsRelationManager;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Schemas\AuditChecklistTemplateForm;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Schemas\AuditChecklistTemplateInfolist;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Tables\AuditChecklistTemplatesTable;
use App\Models\AuditChecklistTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;
use UnitEnum;

class AuditChecklistTemplateResource extends Resource
{
    protected static ?string $model = AuditChecklistTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::Checklist;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Template Checklist';

    protected static ?string $pluralModelLabel = 'Template Checklist';

    protected static string|UnitEnum|null $navigationGroup = 'AMI';

    protected static ?string $navigationLabel = 'Template Checklist';

    public static function form(Schema $schema): Schema
    {
        return AuditChecklistTemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditChecklistTemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditChecklistTemplatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditChecklistTemplates::route('/'),
            'create' => CreateAuditChecklistTemplate::route('/create'),
            'view' => ViewAuditChecklistTemplate::route('/{record}'),
            'edit' => EditAuditChecklistTemplate::route('/{record}/edit'),
        ];
    }
}
