<?php

namespace App\Filament\SuperAdmin\Resources\Roles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Role')
                    ->live(debounce: 0)
                    ->afterStateUpdatedJs(<<<'JS'
                            $set('name', ($state ?? '')
                                .toLowerCase()
                                .replace(/\s+/g, '-'))
                        JS),
            ]);
    }
}
