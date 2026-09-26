<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Nwidart\Modules\Facades\Module;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->spa();

        // Dynamically register active modules' Filament Plugins
        $this->registerActiveModulePlugins($panel);

        return $panel;
    }

    protected function registerActiveModulePlugins(Panel $panel): void
    {
        try {
            $enabledModules = Module::allEnabled();

            foreach ($enabledModules as $module) {
                $filamentConfig = $module->get('filament');
                $pluginClass = is_array($filamentConfig) ? ($filamentConfig['plugin'] ?? null) : null;

                // Conventional fallback if not specified in module.json
                if (!$pluginClass) {
                    $candidate = "Modules\\{$module->getName()}\\{$module->getName()}Plugin";
                    if (class_exists($candidate)) {
                        $pluginClass = $candidate;
                    }
                }

                if ($pluginClass && class_exists($pluginClass)) {
                    if (method_exists($pluginClass, 'make')) {
                        $panel->plugin($pluginClass::make());
                    } else {
                        $panel->plugin(new $pluginClass());
                    }
                }
            }
        } catch (\Throwable $e) {
            // Failsafe during bootstrap / setup
        }
    }
}
