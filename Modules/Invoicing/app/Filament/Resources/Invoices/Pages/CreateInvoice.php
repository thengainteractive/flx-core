<?php

namespace Modules\Invoicing\Filament\Resources\Invoices\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Invoicing\Filament\Resources\Invoices\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    public function getMaxContentWidth(): \Filament\Support\Enums\Width|string|null
    {
        return \Filament\Support\Enums\Width::Full;
    }
}
