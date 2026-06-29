<?php

namespace App\Filament\SuperAdmin\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Nama'),
                TextEntry::make('username')->label('Username'),
                TextEntry::make('email')->label('Email'),
                IconEntry::make('is_active')->label('Aktif')->boolean(),
            ]);
    }
}
