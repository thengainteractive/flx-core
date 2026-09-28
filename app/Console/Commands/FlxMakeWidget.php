<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FlxMakeWidget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flx:make-widget 
                            {name : The name of the widget}
                            {module : The name of the module (e.g. Blog)}
                            {--resource= : The resource to create the widget in}
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Filament widget directly inside an FLX module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $module = Str::studly($this->argument('module'));

        // Validate Module Exists
        $modulePath = base_path("Modules/{$module}");
        if (!is_dir($modulePath)) {
            $this->error("Module [{$module}] does not exist.");
            return self::FAILURE;
        }

        $this->info("Scaffolding Widget [{$name}] for Module [{$module}]...");

        // 1. Run native Filament command
        $args = [
            'name' => $name,
            '--no-interaction' => true,
        ];
        
        if ($this->option('resource')) $args['--resource'] = $this->option('resource');
        if ($this->option('force')) $args['--force'] = true;

        $exitCode = Artisan::call('make:filament-widget', $args, $this->output);

        if ($exitCode !== self::SUCCESS) {
            $this->error("Filament widget generation failed.");
            return $exitCode;
        }

        // 2. Paths
        $resourceFolder = $this->option('resource') ? Str::studly($this->option('resource')) . "Resource/Widgets/" : "";
        $tempFile = app_path("Filament/Widgets/{$resourceFolder}{$name}.php");
        
        $targetFile = base_path("Modules/{$module}/app/Filament/Widgets/{$resourceFolder}{$name}.php");
        $targetDir = dirname($targetFile);

        if (!is_dir($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        // 3. Move file
        if (file_exists($tempFile)) {
            File::move($tempFile, $targetFile);
        }

        // 4. Refactor Namespaces
        $this->refactorNamespaces($targetFile, $module);

        $this->newLine();
        $this->info("✅ Widget [{$name}] successfully generated inside Modules/{$module}");

        return self::SUCCESS;
    }

    protected function refactorNamespaces(string $filePath, string $module): void
    {
        if (!file_exists($filePath)) return;

        $content = file_get_contents($filePath);

        $content = str_replace(
            "namespace App\\Filament\\Widgets",
            "namespace Modules\\{$module}\\Filament\\Widgets",
            $content
        );

        $content = str_replace(
            "use App\\Filament\\Resources",
            "use Modules\\{$module}\\Filament\\Resources",
            $content
        );

        file_put_contents($filePath, $content);
    }
}
