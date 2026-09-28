<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FlxModuleDisable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flx:module-disable {module : The name of the module to disable}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable an FLX module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $module = $this->argument('module');
        
        $this->warn("Disabling FLX module [{$module}]...");
        
        $exitCode = $this->call('module:disable', [
            'module' => $module
        ]);

        if ($exitCode === self::SUCCESS) {
            $this->newLine();
            $this->info("✅ Module [{$module}] has been disabled successfully.");
        }

        return $exitCode;
    }
}
