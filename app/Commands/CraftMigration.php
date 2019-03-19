<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class CraftMigration extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:migration';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Crafts Migration <name>';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('craft:migration handler');
    }
}
