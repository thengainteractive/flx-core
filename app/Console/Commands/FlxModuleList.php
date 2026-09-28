<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FlxModuleList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flx:module-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all installed FLX modules and their statuses';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        return $this->call('module:list');
    }
}
