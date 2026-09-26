<?php

namespace Modules\Venue\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Venue\Filament\Resources\LocationResource;
use Modules\Venue\Filament\Resources\VenueResource;
use Modules\Venue\Filament\Resources\VenueTypeResource;

class VenuePlugin implements Plugin
{
    public function getId(): string
    {
        return 'venue';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                LocationResource::class,
                VenueTypeResource::class,
                VenueResource::class,
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
