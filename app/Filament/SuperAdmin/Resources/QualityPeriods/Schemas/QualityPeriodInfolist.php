<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriods\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class QualityPeriodInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code')->label('Kode'),
                TextEntry::make('name')->label('Nama'),
                TextEntry::make('start_date')->label('Tanggal Mulai')->date(),
                TextEntry::make('end_date')->label('Tanggal Selesai')->date(),
                TextEntry::make('status')->label('Status')->badge(),
                IconEntry::make('is_active')->label('Aktif')->boolean(),
            ]);
    }
}
