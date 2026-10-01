<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
}
