<?php

namespace App\Filament\SuperAdmin\Resources\OrgUnits\Schemas;

use App\Models\OrganizationUnit;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class OrgUnitForm
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
                Grid::make(2)->schema([
                    Select::make('type')
                        ->label('Jenis Organisasi')
                        ->options([
                            'POLITEKNIK' => 'POLITEKNIK',
                            'JURUSAN' => 'JURUSAN',
                            'PROGRAM STUDI' => 'PROGRAM STUDI',
                            'UPM' => 'UPM',
                            'GKM' => 'GKM',
                            'P3M' => 'P3M',
                            'SPI' => 'SPI',
                            'CDC' => 'CDC',
                            'UNIT' => 'UNIT',
                        ])
                        ->native(false),
                    Select::make('parent_id')
                        ->label('Induk Organisasi')
                        ->relationship('parent', 'name')
                        ->searchable()
                        ->preload()
                        // hanya tampil jika bukan POLITEKNIK
                        ->visible(fn (Get $get) => $get('type') !== 'POLITEKNIK')
                        // wajib jika bukan POLITEKNIK
                        ->required(fn (Get $get) => $get('type') !== 'POLITEKNIK')
                        // disable jika belum ada parent
                        ->disabled(fn () => OrganizationUnit::count() === 0)
                        ->helperText(function () {
                            return OrganizationUnit::count() === 0
                                ? 'Belum ada organisasi induk. Buat POLITEKNIK terlebih dahulu.'
                                : null;
                        }),
                    TextInput::make('name')->label('Nama Organisasi'),
                    TextInput::make('code')->label('Kode Organisasi'),
                ])->columnSpanFull(),
            ]);
    }
}
