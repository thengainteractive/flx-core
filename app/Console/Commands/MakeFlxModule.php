<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeFlxModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flx:make-module {name : The name of the module} {--description= : Brief description of the module}';

    protected $description = 'Generate a new self-contained FLX module with Filament plugin support';

    public function handle(): int
    {
        $name = str($this->argument('name'))->studly()->toString();
        $description = $this->option('description') ?: "{$name} module for FLX platform";

        $this->info("Creating FLX module [{$name}]...");

        // 1. Scaffold module via nwidart
        $this->call('module:make', ['name' => [$name]]);

        // 2. Add filament configuration to module.json
        $moduleJsonPath = base_path("Modules/{$name}/module.json");
        if (file_exists($moduleJsonPath)) {
            $data = json_decode(file_get_contents($moduleJsonPath), true);
            $data['description'] = $description;
            $data['filament'] = [
                'plugin' => "Modules\\{$name}\\{$name}Plugin",
            ];
            file_put_contents($moduleJsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        // 3. Create Filament Plugin class
        $pluginDir = base_path("Modules/{$name}/app");
        $pluginClassFile = "{$pluginDir}/{$name}Plugin.php";
        $pluginContent = <<<PHP
<?php

namespace Modules\\{$name};

use Filament\Contracts\Plugin;
use Filament\Panel;

class {$name}Plugin implements Plugin
{
    public function getId(): string
    {
        return '{$name}';
    }

    public function register(Panel \$panel): void
    {
        // Auto-discover or register module Filament resources here
        \$panel->discoverResources(
            in: module_path('{$name}', 'app/Filament/Resources'),
            for: "Modules\\\\{$name}\\\\Filament\\\\Resources"
        );
    }

    public function boot(Panel \$panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
PHP;
        file_put_contents($pluginClassFile, $pluginContent);

        // 4. Create Filament directories
        $filamentDirs = [
            base_path("Modules/{$name}/app/Filament/Resources"),
            base_path("Modules/{$name}/app/Filament/Pages"),
            base_path("Modules/{$name}/app/Filament/Widgets"),
        ];
        foreach ($filamentDirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $this->newLine();
        $this->info("✅ Module [{$name}] successfully generated with Filament Plugin support!");
        $this->line("   - Path: Modules/{$name}");
        $this->line("   - Plugin: Modules\\{$name}\\{$name}Plugin");
        $this->line("   - Resources folder: Modules/{$name}/app/Filament/Resources");
        $this->line("   - Filament Dashboard toggle: Available in Admin -> Settings -> Modules");

        return self::SUCCESS;
    }
}
