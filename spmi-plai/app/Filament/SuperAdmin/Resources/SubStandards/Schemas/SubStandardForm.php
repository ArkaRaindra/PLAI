<?php

namespace App\Filament\SuperAdmin\Resources\SubStandards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubStandardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('standard_id')
                    ->relationship('standard', 'name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                Textarea::make('indicator')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('max_score')
                    ->required()
                    ->numeric()
                    ->default(4),
            ]);
    }
}
