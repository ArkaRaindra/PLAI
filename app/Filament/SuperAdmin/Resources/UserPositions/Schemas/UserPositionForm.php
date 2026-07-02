<?php

namespace App\Filament\SuperAdmin\Resources\UserPositions\Schemas;

use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\User;
use App\Models\UserPosition;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class UserPositionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('is_active')
                    ->label('Status Jabatan')
                    ->options([
                        true => 'Aktif',
                        false => 'Tidak Aktif',
                    ])
                    ->default(true)
                    ->required()
                    ->columnSpanFull(),
                Select::make('user_id')
                    ->label('Nama Pengguna')
                    ->options(function (?UserPosition $record): array {
                        return User::query()
                            ->where(function ($query) use ($record): void {
                                $query->where('is_active', true);

                                if ($record?->user_id) {
                                    $query->orWhere('id', $record->user_id);
                                }
                            })
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('position_id')
                    ->label('Jabatan')
                    ->options(Position::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('organization_unit_id')
                    ->label('Unit Organisasi')
                    ->options(OrganizationUnit::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->default(now())
                    ->required()
                    ->native(false),
                DatePicker::make('end_date')
                    ->label('Tanggal Berakhir')
                    ->required()
                    ->native(false),
            ]);
    }
}
