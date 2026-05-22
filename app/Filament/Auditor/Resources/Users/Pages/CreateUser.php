<?php

namespace App\Filament\Auditor\Resources\Users\Pages;

use App\Filament\Auditor\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
