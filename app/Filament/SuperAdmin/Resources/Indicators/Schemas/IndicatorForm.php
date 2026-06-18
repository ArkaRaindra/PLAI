<?php

namespace App\Filament\SuperAdmin\Resources\Indicators\Schemas;

use App\Models\Indicator;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IndicatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sub_standard_id')
                    ->relationship('subStandard', 'code')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->unique(Indicator::class, ignoreRecord: true),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
