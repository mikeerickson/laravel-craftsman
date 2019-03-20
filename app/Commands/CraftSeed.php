<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class CraftSeed extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:seed';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Crafts seed <name>';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('craft:seed handler');
    }
}
