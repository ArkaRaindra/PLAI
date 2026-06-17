<?php

namespace App\Filament\SuperAdmin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(),
                Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload(),
                Select::make('faculty_id')
                    ->label('Fakultas')
                    ->relationship('faculty', 'name')
                    ->searchable()
                    ->preload()
                    ->live(),
                Select::make('study_program_id')
                    ->label('Program Studi')
                    ->relationship('studyProgram', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('period_id')
                    ->relationship('period', 'name'),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
