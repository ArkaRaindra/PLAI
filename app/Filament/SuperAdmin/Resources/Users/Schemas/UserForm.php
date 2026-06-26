<?php

namespace App\Filament\SuperAdmin\Resources\Users\Schemas;

use App\Models\Position;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('username')
                        ->label('Username')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->default('password')
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->minLength(8)
                        ->dehydrated(fn (?string $state): bool => filled($state)),
                    Toggle::make('is_active')
                        ->label('Active Status')
                        ->default(true)
                        ->required(),
                    Select::make('roles')
                        ->label('Role')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->required(),
                ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make([
                    Repeater::make('userPosition')
                        ->label('User Position')
                        ->relationship('userPositions')
                        ->schema([
                            Select::make('position_id')
                                ->label('Jabatan')
                                ->relationship('position', 'name')
                                ->preload()
                                ->searchable()
                                ->required(),
                            Select::make('organization_unit_id')
                                ->label('Unit Organisasi')
                                ->relationship('organizationUnit', 'name')
                                ->preload()
                                ->searchable()
                                ->required(),
                            DatePicker::make('start_date')
                                ->label('Start Date')
                                ->default(now())
                                ->displayFormat('d/m/Y')
                                ->required(),
                            DatePicker::make('end_date')
                                ->label('End Date')
                                ->displayFormat('d/m/Y')
                                ->afterOrEqual('start_date'),
                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true)
                                ->required(),
                        ])
                        ->columns(2)
                        ->addActionLabel('Tambah Jabatan')
                        ->collapsible()
                        ->defaultItems(0)
                        ->itemLabel(fn (array $state): ?string => isset($state['position_id'])
                                ? (Position::find($state['position_id'])?->name ?? 'Jabatan Baru')
                                : 'Jabatan Baru')
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            $data['created_by'] = auth()->id();
                            $data['updated_by'] = auth()->id();

                            return $data;
                        })
                        ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                            $data['updated_by'] = auth()->id();

                            return $data;
                        }),
                ])
                    ->columnSpanFull(),
            ]);
    }
}
