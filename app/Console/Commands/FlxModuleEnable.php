<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FlxModuleEnable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flx:module-enable {module : The name of the module to enable}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable an FLX module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $module = $this->argument('module');
        
        $this->info("Enabling FLX module [{$module}]...");
        
        $exitCode = $this->call('module:enable', [
            'module' => $module
        ]);

        if ($exitCode === self::SUCCESS) {
            $this->newLine();
            $this->info("✅ Module [{$module}] has been enabled successfully.");
        }

        return $exitCode;
    }
}
