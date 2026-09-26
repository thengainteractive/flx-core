<?php

namespace Modules\Blog\Filament\Resources\Categories\Pages;

use Modules\Blog\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
