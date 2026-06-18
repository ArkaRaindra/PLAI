<?php

namespace App\Filament\SuperAdmin\Resources\Users\Schemas;

use App\Models\Faculty;
use App\Models\StudyProgram;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

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
                TextInput::make('username')
                    ->label('Username')
                    ->required(),
                DateTimePicker::make('email_verified_at')
                    ->default(now())
                    ->disabled()
                    ->hidden(),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required(),
                Select::make('faculty_id')
                    ->label('Fakultas')
                    ->options(fn () => Faculty::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live(),
                Select::make('study_program_id')
                    ->label('Program Studi')
                    ->options(fn (callable $get): Collection => StudyProgram::query()
                        ->when($get('faculty_id'), fn ($query) => $query->where('faculty_id', $get('faculty_id')))
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                Select::make('period_id')
                    ->label('Periode')
                    ->relationship('period', 'name'),
                Checkbox::make('is_active')
                    ->default(true),
            ]);
    }
}
