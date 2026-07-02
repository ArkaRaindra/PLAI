<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Schemas;

use App\Models\OrganizationUnit;
use App\Models\Target;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RealizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
//
            ]);
    }
}
