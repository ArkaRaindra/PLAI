<?php

namespace App\Filament\Prodi\Resources\AuditScores;

use App\Filament\Prodi\Resources\AuditScores\Pages\CreateAuditScore;
use App\Filament\Prodi\Resources\AuditScores\Pages\EditAuditScore;
use App\Filament\Prodi\Resources\AuditScores\Pages\ListAuditScores;
use App\Filament\Prodi\Resources\AuditScores\Schemas\AuditScoreForm;
use App\Filament\Prodi\Resources\AuditScores\Tables\AuditScoresTable;
use App\Models\AuditScore;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AuditScoreResource extends Resource
{
    protected static ?string $model = AuditScore::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static string|\UnitEnum|null $navigationGroup = 'Penilaian & Diagram';

    protected static ?string $pluralLabel = 'Diagram Pencapaian';

    protected static ?string $navigationLabel = 'Penilaian & Diagram';

    public static function form(Schema $schema): Schema
    {
        return AuditScoreForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditScoresTable::configure($table);
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
            'index' => ListAuditScores::route('/'),
            'create' => CreateAuditScore::route('/create'),
            'edit' => EditAuditScore::route('/{record}/edit'),
        ];
    }
}
