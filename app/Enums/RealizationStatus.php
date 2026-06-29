<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RealizationStatus: string implements HasColor, HasLabel
{
    case Planned = 'planned';
    case Ongoing = 'ongoing';
    case Completed = 'completed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Planned => 'Direncanakan',
            self::Ongoing => 'Berjalan',
            self::Completed => 'Selesai',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Planned => 'info',
            self::Ongoing => 'warning',
            self::Completed => 'success',
        };
    }
}
