<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Automatically autoload any module classes under Modules/{Name}/app/
        spl_autoload_register(function ($class) {
            if (str_starts_with($class, 'Modules\\')) {
                $parts = explode('\\', $class);
                if (count($parts) >= 2) {
                    $moduleName = $parts[1];
                    $subPath = implode(DIRECTORY_SEPARATOR, array_slice($parts, 2));
                    $filePath = base_path("Modules/{$moduleName}/app/{$subPath}.php");
                    if (file_exists($filePath)) {
                        require_once $filePath;
                        return true;
                    }
                }
            }
            return false;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
