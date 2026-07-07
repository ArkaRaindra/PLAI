<?php

namespace App\Filament\SuperAdmin\Resources\StandarSources\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StandarSourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'AKTIF',
                        0 => 'TIDAK AKTIF',
                    ])
                    ->default(true)
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->extraInputAttributes([
                        'style' => 'text-transform: uppercase',
                    ])
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }
}
