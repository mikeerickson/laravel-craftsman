<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class CraftController extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:controller';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description ='craft:controller <name>';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('craft:controller handler');
    }
}
