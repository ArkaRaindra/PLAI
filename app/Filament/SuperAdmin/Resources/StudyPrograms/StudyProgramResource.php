<?php

namespace App\Filament\SuperAdmin\Resources\StudyPrograms;

use App\Filament\SuperAdmin\Resources\StudyPrograms\Pages\CreateStudyProgram;
use App\Filament\SuperAdmin\Resources\StudyPrograms\Pages\EditStudyProgram;
use App\Filament\SuperAdmin\Resources\StudyPrograms\Pages\ListStudyPrograms;
use App\Filament\SuperAdmin\Resources\StudyPrograms\Schemas\StudyProgramForm;
use App\Filament\SuperAdmin\Resources\StudyPrograms\Tables\StudyProgramsTable;
use App\Models\StudyProgram;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StudyProgramResource extends Resource
{
    protected static ?string $model = StudyProgram::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static string|\UnitEnum|null $navigationGroup = 'Masters';

    protected static ?string $navigationLabel = 'Program Studi';

    protected static ?string $pluralLabel = 'Program Studi';

    public static function form(Schema $schema): Schema
    {
        return StudyProgramForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudyProgramsTable::configure($table);
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
            'index' => ListStudyPrograms::route('/'),
            'create' => CreateStudyProgram::route('/create'),
            'edit' => EditStudyProgram::route('/{record}/edit'),
        ];
    }
}
