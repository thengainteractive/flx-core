<?php

namespace Modules\Blog\Filament\Resources\Categories\Pages;

use Modules\Blog\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;
}
