<?php

namespace App\Filament\SuperAdmin\Resources\Faculties;

use App\Filament\SuperAdmin\Resources\Faculties\Pages\CreateFaculty;
use App\Filament\SuperAdmin\Resources\Faculties\Pages\EditFaculty;
use App\Filament\SuperAdmin\Resources\Faculties\Pages\ListFaculties;
use App\Filament\SuperAdmin\Resources\Faculties\Schemas\FacultyForm;
use App\Filament\SuperAdmin\Resources\Faculties\Tables\FacultiesTable;
use App\Models\Faculty;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FacultyResource extends Resource
{
    protected static ?string $model = Faculty::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingLibrary;

    protected static string|\UnitEnum|null $navigationGroup = 'Masters';

    protected static ?string $navigationLabel = 'Fakultas';

    protected static ?string $pluralLabel = 'Fakultas';

    public static function form(Schema $schema): Schema
    {
        return FacultyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FacultiesTable::configure($table);
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
            'index' => ListFaculties::route('/'),
            'create' => CreateFaculty::route('/create'),
            'edit' => EditFaculty::route('/{record}/edit'),
        ];
    }
}
