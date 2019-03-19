<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class MakeFactory extends Command
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
    protected $description = 'Crafts Factory';

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
