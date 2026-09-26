<?php

namespace Modules\Invoicing;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Invoicing\Filament\Resources\InvoiceResource;

class InvoicingPlugin implements Plugin
{
    public function getId(): string
    {
        return 'invoicing';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            \Modules\Invoicing\Filament\Resources\Invoices\InvoiceResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
