<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FlxModuleMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flx:module-migrate {module? : The name of the module to migrate} {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database migrations for a specific FLX module or all enabled modules';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $module = $this->argument('module');
        $force = $this->option('force');
        
        if ($module) {
            $this->info("Running migrations for FLX module [{$module}]...");
            $args = ['module' => $module];
        } else {
            $this->info("Running migrations for all enabled FLX modules...");
            $args = [];
        }

        if ($force) {
            $args['--force'] = true;
        }

        $exitCode = $this->call('module:migrate', $args);

        if ($exitCode === self::SUCCESS) {
            $this->newLine();
            $this->info("✅ Migrations completed successfully.");
        }

        return $exitCode;
    }
}
