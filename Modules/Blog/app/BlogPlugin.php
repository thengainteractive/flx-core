<?php

namespace Modules\Blog;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Blog\Filament\Resources\Categories\CategoryResource;
use Modules\Blog\Filament\Resources\Posts\PostResource;

class BlogPlugin implements Plugin
{
    public function getId(): string
    {
        return 'blog';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PostResource::class,
            CategoryResource::class,
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
