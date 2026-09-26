<?php

namespace Modules\Contacts;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Contacts\Filament\Resources\ContactResource;

class ContactsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'contacts';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            \Modules\Contacts\Filament\Resources\Contacts\ContactResource::class,
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
