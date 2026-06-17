<?php

namespace App\Filament\SuperAdmin\Resources\Faculties\Schemas;

use App\Models\Faculty;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FacultyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Fakultas')
                    ->required()
                    ->maxLength(255)
                    ->unique(Faculty::class),
            ]);
    }
}
