<?php

namespace App\Filament\SuperAdmin\Resources\StudyPrograms\Schemas;

use App\Models\StudyProgram;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudyProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('faculty_id')
                    ->label('Fakultas')
                    ->relationship('faculty', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label('Program Studi')
                    ->required()
                    ->maxLength(255)
                    ->unique(StudyProgram::class),
            ]);
    }
}
