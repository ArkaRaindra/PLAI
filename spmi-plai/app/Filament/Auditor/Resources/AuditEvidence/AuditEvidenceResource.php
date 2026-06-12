<?php

namespace App\Filament\Auditor\Resources\AuditEvidence;

use App\Filament\Auditor\Resources\AuditEvidence\Pages\CreateAuditEvidence;
use App\Filament\Auditor\Resources\AuditEvidence\Pages\EditAuditEvidence;
use App\Filament\Auditor\Resources\AuditEvidence\Pages\ListAuditEvidence;
use App\Filament\Auditor\Resources\AuditEvidence\Schemas\AuditEvidenceForm;
use App\Filament\Auditor\Resources\AuditEvidence\Tables\AuditEvidenceTable;
use App\Models\AuditEvidence;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Override;

class AuditEvidenceResource extends Resource
{
    protected static ?string $model = AuditEvidence::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $navigationLabel = 'Validasi Bukti';

    protected static ?string $pluralLabel = 'Validasi Bukti';

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('auditor');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'submitted')
            ->latest();
    }

    public static function form(Schema $schema): Schema
    {
        return AuditEvidenceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditEvidenceTable::configure($table);
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
            'index' => ListAuditEvidence::route('/'),
            'edit' => EditAuditEvidence::route('/{record}/edit'),
        ];
    }
}
