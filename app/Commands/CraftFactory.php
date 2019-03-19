<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class CraftFactory extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:factory';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Crafts Factory <name>';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('craft:factory handler');
    }
}
