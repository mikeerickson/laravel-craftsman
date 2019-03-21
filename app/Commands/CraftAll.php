<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class CraftAll extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:all';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'craft:all
                     name             The name of the resource (required)
                     --model-path     Alternate path to models (default: ./app)
                     --no-controller  Do not create controller
                     --no-factory     Do not create factory
                     --no-migration   Do not create migration
                     --no-model       Do not create model
            ';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('craft:all handler');
    }
}
