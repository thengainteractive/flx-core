<?php

namespace Modules\Page;

use Filament\Contracts\Plugin;
use Filament\Panel;

class PagePlugin implements Plugin
{
    public function getId(): string
    {
        return 'Page';
    }

    public function register(Panel $panel): void
    {
        // Auto-discover or register module Filament resources here
        $panel->discoverResources(
            in: module_path('Page', 'app/Filament/Resources'),
            for: "Modules\\Page\\Filament\\Resources"
        );
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