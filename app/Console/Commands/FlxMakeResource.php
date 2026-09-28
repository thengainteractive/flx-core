<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FlxMakeResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flx:make-resource 
                            {name : The name of the resource (e.g. Post)}
                            {module : The name of the module (e.g. Blog)}
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Filament resource directly inside an FLX module using custom stubs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $module = Str::studly($this->argument('module'));
        $resourceName = "{$name}Resource";

        // Validate Module Exists
        $modulePath = base_path("Modules/{$module}");
        if (!is_dir($modulePath)) {
            $this->error("Module [{$module}] does not exist. Create it first using flx:make-module.");
            return self::FAILURE;
        }

        $this->info("Scaffolding {$resourceName} for Module [{$module}] natively...");

        // Setup paths
        $baseResourcePath = "{$modulePath}/app/Filament/Resources/{$resourceName}";
        $pagesPath = "{$baseResourcePath}/Pages";
        
        // Ensure directories exist
        if (!is_dir($pagesPath)) {
            File::makeDirectory($pagesPath, 0755, true);
        }

        // Variables for stub replacement
        $pluralModelClass = Str::plural($name);
        
        $replacements = [
            '{{ module }}' => $module,
            '{{ modelClass }}' => $name,
            '{{ resourceClass }}' => $resourceName,
            '{{ pluralModelClass }}' => $pluralModelClass,
        ];

        // Files to generate
        $filesToGenerate = [
            base_path('stubs/flx/filament/Resource.stub') => "{$baseResourcePath}.php",
            base_path('stubs/flx/filament/List.php.stub') => "{$pagesPath}/List{$pluralModelClass}.php",
            base_path('stubs/flx/filament/Create.php.stub') => "{$pagesPath}/Create{$name}.php",
            base_path('stubs/flx/filament/Edit.php.stub') => "{$pagesPath}/Edit{$name}.php",
        ];

        $force = $this->option('force');

        foreach ($filesToGenerate as $stub => $destination) {
            if (file_exists($destination) && !$force) {
                $this->warn("File already exists, skipping: " . basename($destination));
                continue;
            }

            if (!file_exists($stub)) {
                $this->error("Stub file not found: {$stub}");
                return self::FAILURE;
            }

            $content = file_get_contents($stub);
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
            
            file_put_contents($destination, $content);
        }

        $this->newLine();
        $this->info("✅ {$resourceName} successfully generated natively inside Modules/{$module}");
        $this->line("   - Path: Modules/{$module}/app/Filament/Resources/{$resourceName}.php");
        $this->line("   - Navigation Group: Hardcoded to '{$module}' via stub");

        return self::SUCCESS;
    }
}
