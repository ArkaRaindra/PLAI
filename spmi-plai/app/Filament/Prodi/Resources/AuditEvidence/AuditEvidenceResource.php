<?php

namespace App\Filament\Prodi\Resources\AuditEvidence;

use App\Filament\Prodi\Resources\AuditEvidence\Pages\CreateAuditEvidence;
use App\Filament\Prodi\Resources\AuditEvidence\Pages\EditAuditEvidence;
use App\Filament\Prodi\Resources\AuditEvidence\Pages\ListAuditEvidence;
use App\Filament\Prodi\Resources\AuditEvidence\Schemas\AuditEvidenceForm;
use App\Filament\Prodi\Resources\AuditEvidence\Tables\AuditEvidenceTable;
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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Document;

    protected static ?string $navigationLabel = 'Bukti Audit';

    protected static ?string $pluralLabel = 'Bukti Audit';

    #[Override]
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('prodi');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
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
            'create' => CreateAuditEvidence::route('/create'),
            'edit' => EditAuditEvidence::route('/{record}/edit'),
        ];
    }
}
