<?php

namespace Modules\Catalog\Filament\Resources\Products\Pages;

use Modules\Catalog\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;
}
