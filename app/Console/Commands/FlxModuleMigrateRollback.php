<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FlxModuleMigrateRollback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flx:module-migrate-rollback {module? : The name of the module to rollback} {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback database migrations for a specific FLX module or all modules';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $module = $this->argument('module');
        $force = $this->option('force');
        
        if ($module) {
            $this->warn("Rolling back migrations for FLX module [{$module}]...");
            $args = ['module' => $module];
        } else {
            $this->warn("Rolling back migrations for all FLX modules...");
            $args = [];
        }

        if ($force) {
            $args['--force'] = true;
        }

        $exitCode = $this->call('module:migrate-rollback', $args);

        if ($exitCode === self::SUCCESS) {
            $this->newLine();
            $this->info("✅ Migrations rolled back successfully.");
        }

        return $exitCode;
    }
}
