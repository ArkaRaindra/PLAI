<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriodes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QualityPeriodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')->label('Kode')->required()->maxLength(50),
                TextInput::make('name')->label('Nama')->required()->maxLength(255),
                DatePicker::make('start_date')->label('Tanggal Mulai')->required()->native(false),
                DatePicker::make('end_date')->label('Tanggal Selesai')->required()->native(false),
                Select::make('status')->label('Status')->options([
                    'draft' => 'Draft',
                    'active' => 'Aktif',
                    'closed' => 'Selesai',
                ])->required()->native(false),
            ]);
    }
}
