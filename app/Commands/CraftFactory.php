<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftFactory
 * @package App\Commands
 */
class CraftFactory extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:factory 
                                {name : Factory Name} 
                                {--m|model= : Associated model}
                                {--w|overwrite : Overwrite existing factory}
                            ';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Factory
                     <name>               Factory Name
                     --model, -m          Use <model> when creating controller
                     --overwrite, -w      Overwrite existing factory
            ';

    /**
     * CraftFactory constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $factoryName = $this->argument('name');
        $model = $this->option('model');

        if (strlen($model) === 0) {
            $this->error("Must supply model name");
        } else {
            $data = [
                "model" => $model,
                "name" => $factoryName,
                "overwrite" => $this->option('overwrite'),
            ];

            $this->fs->createFile('factory', $factoryName, $data);
        }
    }
}
