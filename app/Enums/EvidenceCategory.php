<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EvidenceCategory: string implements HasLabel
{
    case Sop = 'sop';
    case Sk = 'sk';
    case Pedoman = 'pedoman';
    case Laporan = 'laporan';
    case Audit = 'audit';
    case Rtm = 'rtm';
    case Kerjasama = 'kerjasama';
    case Penelitian = 'penelitian';
    case Pkm = 'pkm';
    case Akreditasi = 'akreditasi';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Sop => 'SOP',
            self::Sk => 'SK',
            self::Pedoman => 'Pedoman',
            self::Laporan => 'Laporan',
            self::Audit => 'Audit',
            self::Rtm => 'RTM',
            self::Kerjasama => 'Kerjasama',
            self::Penelitian => 'Penelitian',
            self::Pkm => 'PKM',
            self::Akreditasi => 'Akreditasi',
        };
    }
}