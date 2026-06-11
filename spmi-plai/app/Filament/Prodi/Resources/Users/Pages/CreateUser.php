<?php

namespace App\Filament\Prodi\Resources\Users\Pages;

use App\Filament\Prodi\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
